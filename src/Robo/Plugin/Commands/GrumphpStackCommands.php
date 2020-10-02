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
                    ->exec('remove ' . implode(' ', $toRemove) . ' --ansi');
            }

            // Change version of quizlabs/php_codesniffer to 3.x-dev.
            unset($suggests['symplify/easycodingstandard']);
            unset($suggests['squizlabs/php_codesniffer']);
            // The codegyre/robo package is now consolidation/robo.
            unset($suggests['codegyre/robo']);
            // The infection/infection package conflicts with phpunit/phpunit.
            unset($suggests['infection/infection']);
            // The sstalle/php7cc package conflicts with nikic/php-parser.
            unset($suggests['sstalle/php7cc']);
            // The malukenho/kawaii-gherkin conflicts with vimeo/psalm because
            // of an old sebastian/diff ~1.2 requirement. I would like to
            // resolve that one.
            unset($suggests['malukenho/kawaii-gherkin']);
            // The povils/phpmnd package conflicts with phpunit/phpunit because
            // of an older phpunit/php-timer ^2.0||^3.0 requirement. I would
            // like to resolve that one.
            unset($suggests['povils/phpmnd']);
            // The pestphp/pest package conflicts with phpunit/phpunit req.
            unset($suggests['pestphp/pest']);
            // Unset friendsoftwig/twigcs to make the requirement higher.
            unset($suggests['friendsoftwig/twigcs']);

            $packages = array_keys($suggests);
            // Change version and package names:
            $packages[] = 'friendsoftwig/twigcs:>=4';
            $packages[] = 'squizlabs/php_codesniffer:3.x-dev';
            $packages[] = 'symplify/easy-coding-standard';
            $packages[] = 'consolidation/robo';
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
