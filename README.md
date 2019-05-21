------------------------------------------------------------------------------

# CashInCashOut :3.14

- Author: Artiom Sreda
- Email: artiomsreda@gmail.com

```
DEV NOTE: if you want use for commercial purpose, please contact with author
```

------------------------------------------------------------------------------

### Development environment (tested system requirements):

- Win7 x64,
- XAMPP v3.2.3
- PHP 7.3.4
- Composer version 1.8.5
- PHP Unit Tests version 8

------------------------------------------------------------------------------

### More info for XAMPP users on Win (can help):

- In php.ini file remove (;) before next lines:

```
;extension=php_openssl.dll (for example if using $: php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');")
;extension=php_curl.dll (if using cURL from terminal for example $: curl -sS https://getcomposer.org/installer | php )
```

------------------------------------------------------------------------------

### Install:

- Copy "install.php" file to server in project root directory and run command
```
$: php install.php
```
- If something goes wrong, see below "Manual installation"

------------------------------------------------------------------------------

### Manual installation:

- More info: https://getcomposer.org/download/

- Command-line installation (next several commands prepare project <...> and create composer.phar file - tested on Windows):
```
$: php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
$: php -r "if (hash_file('sha384', 'composer-setup.php') === '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
$: php composer-setup.php
$: php -r "unlink('composer-setup.php');" 
```

- More info: https://getcomposer.org/doc/03-cli.md#init
- After all, for automatically create "composer.json" file, run command
``` 
$: php composer.phar init
```
- Copy & Paste to created "composer.json" file this:

```
{
  "autoload": {
    "psr-4": {
      "Cashincashout\\": "app/Cashincashout"
    },
    "classmap": [
      "tests/",
      "AppLoadComposerHelper.php",
      "Index.php"
    ]
  },
  "scripts": {
    "call-app": [
      "AppLoadComposerHelper::argsHandlerComposer"
    ],
    "call-tests": [
      "vendor\\bin\\phpunit --bootstrap ./vendor/autoload.php ./tests/WeekCheckerHelperTest",
      "vendor\\bin\\phpunit --bootstrap ./vendor/autoload.php ./tests/CSVModelTest"
    ]
  },
  "require-dev": {
    "composer/composer": "1.8.5",
    "phpunit/phpunit": "8"
  }
}
```
- After all, you can run command 
```
$: php composer.phar install
$: php composer.phar update
$: php composer.phar upgrade
```

------------------------------------------------------------------------------

### Additional info:
 
- Build / rebuild autoload.php with composer, run command 
```
$: composer dump-autoload -o
``` 
It's creates vendors directory classes autoload file <...> 
 
------------------------------------------------------------------------------

# How use/run app?
 
1. After configurate ../psr4/app on server 
2. Open system terminal and go to ../psr4/ directory
3. App can loads by two ways (for dev see in index.php):
    - 3.1. Run command ```$: php index.php```
    - 3.2. Or run command ```$: composer call-app```
4. After all, terminal will output steps messages

------------------------------------------------------------------------------

### Structure:

- psr4
    - app
        - Cashincashout
            - Helpers
                - <...>
            - Models
                - <...>    
            - BaseController.php                    
        - data
            - input.csv        
    - tests (phpunit tests)        
    - vendor
        - <...>
    - composer.json
    - index.php   
    - AppLoadComposerHelper.php  
    - README.md    
    
------------------------------------------------------------------------------

# PhpUnit Tests:
- Load all phpunit tests automaticaly:
```
$: composer call-tests
```
- Load phpunit tests manualy:
```
$: vendor\bin\phpunit --bootstrap ./vendor/autoload.php ./tests/WeekCheckerHelperTest
```
- The flag --verbose return more info about skipped tests methods. Use example:
```
$: vendor\bin\phpunit --bootstrap ./vendor/autoload.php ./tests/WeekCheckerHelperTest --verbose
```
------------------------------------------------------------------------------

### Tips (paths to scripts issues):

- If command ```$: vendor\bin\phpunit tests/HERE_UNIT_TEST_FILE```
return: Fatal error: Uncaught TypeError: fopen() expects parameter 1 to be a valid path, bool given in psr4\vendor\phpunit\phpunit\src\Util\FileLoader.php:37

- Then try run 
```
$: vendor\bin\phpunit tests/HERE_UNIT_TEST_FILE.php
``` 
- Or try run:
```
$: vendor\bin\phpunit ./tests/HERE_UNIT_TEST_FILE
```
- If use options, then:
```
$: vendor\bin\phpunit --bootstrap HERE_FULL_PATH_TO_DIR\vendor\autoload.php ./tests/WeekCheckerHelperTest.php
```

------------------------------------------------------------------------------