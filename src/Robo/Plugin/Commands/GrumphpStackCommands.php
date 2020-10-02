<?php

namespace VerbruggenAlex\GrumphpStack\Robo\Plugin\Commands;

use Symfony\Component\Process\ExecutableFinder;

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class GrumphpStackCommands extends \Robo\Tasks
{
    const GRUMPHP_COMPOSER_URL = 'https://raw.githubusercontent.com/phpro/grumphp/master/composer.json';

    /** @var array $tasks */
    protected $tasks = [];

  /**
   * Generate the composer.json.
   *
   * @return \Robo\Collection\CollectionBuilder|null
   *   Collection builder.
   *
   * @command gs:generate
   */
    public function generateComposerJson()
    {
        // Download the phpro/grumphp composer.json file.
        $tmpDir = getcwd() . '/.tmp/';
        $this->taskExecStack()
            ->stopOnFail()
            ->exec('rm -rf ' . $tmpDir)
            ->exec('mkdir -p ' . $tmpDir)
            ->exec('wget -P ' . $tmpDir . ' ' . self::GRUMPHP_COMPOSER_URL)->run();

        // Get the packages from the suggest section.
        $packages = [];
        $finder = new ExecutableFinder();
        $composerBin = $finder->find('composer');
        $grumphpComposerJson = $tmpDir . 'composer.json';

        if (file_exists($grumphpComposerJson)) {
            $suggests = json_decode(file_get_contents($grumphpComposerJson), true)['suggest'];


            // Remove require section from composer.json to re-require the suggests.
            $libraryComposerJson = json_decode(file_get_contents('composer.json'), true);
            $oldPackages = $libraryComposerJson['require'];
            $toRemove = array_diff_key($oldPackages, $suggests);
            // Do not remove phpro/grumphp itself.
            unset($toRemove['phpro/grumphp']);

            // Remove packages.
            if (count($toRemove) !== 0) {
                $this->tasks[] = $this->taskExecStack()
                    ->stopOnFail()
                    ->executable($composerBin)
                    ->exec('remove ' . implode(' ', array_keys($toRemove)) . ' --ansi');
            }

            // Array with hardcoded removals:
            $toRemoveHardcoded = [
                'friendsoftwig/twigcs', // Unset to increase version.
                'squizlabs/php_codesniffer', // Change version to 3.x-dev.
                'infection/infection', // Conflicts with phpunit/phpunit.
                'sstalle/php7cc', // Conflicts with nikic/php-parser.
                'malukenho/kawaii-gherkin', // Conflicts with vimeo/psalm.
                'povils/phpmnd', // conflicts with phpunit/phpunit.
                'pestphp/pest', // Conflicts with phpunit/phpunit.
                'codeception/codeception', // Conflicts with phpunit/phpunit.
            ];

            $intersectRemoval = array_intersect(array_keys($oldPackages), $toRemoveHardcoded);
            if (count($intersectRemoval) !== 0) {
                $this->tasks[] = $this->taskExecStack()
                ->stopOnFail()
                ->executable($composerBin)
                ->exec('remove ' . implode(' ', $intersectRemoval) . ' --ansi');
            }

            $packages = array_diff(array_keys($suggests), $toRemoveHardcoded);
            // Change version and package names:
            $packages[] = 'friendsoftwig/twigcs:>=4';
            $packages[] = 'squizlabs/php_codesniffer:3.x-dev';
            // Add the phpro/grumphp package.
            $packages[] = 'phpro/grumphp';
        }

        // Require the new suggest packages.
        $this->tasks[] = $this->taskExecStack()
            ->stopOnFail()
            ->executable($composerBin)
            ->exec('require "' . implode('" "', $packages) . '" --prefer-lowest --no-suggest --no-progress --ansi');

        // Normalize the composer.json.
        $this->tasks[] = $this->taskExecStack()
            ->stopOnFail()
            ->executable($composerBin)
            ->exec('normalize --ansi');

        return $this
            ->collectionBuilder()
            ->addTaskList($this->tasks);
    }
}
