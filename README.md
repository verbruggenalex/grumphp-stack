# Grumphp Stack

A Composer library with [Grumphp](https://github.com/phpro/grumphp#grumphp) and
all of it's [suggested packages](https://github.com/phpro/grumphp/blob/master/composer.json#L49).
Currently some of the suggestions are not included in this library because of
conflicts between dependencies of suggestions or missing packages. These are
[hardcoded in the development commands](./src/Robo/Plugin/Commands/GrumphpStackCommands.php#L41).

## Installation

```bash
# This library of packages is intended to be used on the global Composer level.
# To install the packages execute the following command:
composer global require verbruggenalex/grumphp-stack

# Make sure that your global Composer binary is defined in your PATH:
echo $PATH

# If it's not append it to your PATH variable:
export PATH="$PATH:$HOME/.composer/vendor/bin"
```

## Usage

```bash
# If your project does not have a grumphp.yml file configured yet run:
grumphp configure

# If you have a grumphp.yml file configured you can run:
grumphp run
```

## Development

```bash
# Clone repository
git clone git@github.com:verbruggenalex/grumphp-stack.git

# Enter repository folder.
cd grumphp-stack

# Run composer install.
composer install

# Re-generate the composer.json
./vendor/bin/robo gs:generate
```

