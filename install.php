<?php
error_reporting(E_ERROR);
ini_set('display_errors', 1);
define('SOURCE_DIR', __DIR__);
define('HOME_DIR', __DIR__ . '/');
define('COMPOSER_HOME_DIR', HOME_DIR . 'composer/');
define('COMPOSER_SHA384', '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5');
// define if you need access to a private repository on github
// define('OAUTH_TOKEN', '1234');
define('SPLITTER', '--------------------------------------------------------------------------------' . "\n");

chdir(SOURCE_DIR);
builder();

function builder()
{
    if (is_readable('composer.json')) {
        echo SPLITTER;
        echo 'File "composer.json" is found' . "\n";
        echo SPLITTER;
        $output = array();
        putenv('COMPOSER_HOME=' . COMPOSER_HOME_DIR);
        putenv('HOME=' . HOME_DIR);
        if (!is_readable(HOME_DIR . 'composer.phar')) {
            chdir(HOME_DIR);
            copy('https://getcomposer.org/installer', 'composer-setup.php');
            if (hash_file('SHA384', 'composer-setup.php') === COMPOSER_SHA384) {
                echo 'Installer verified. Composer setup is equal SHA384 hash' . "\n";
                echo SPLITTER;
                // If require_once 'composer-setup.php'; - this will terminate request...

                // php composer-setup.php
                $command = 'php ' . HOME_DIR . 'composer-setup.php';
                $output[] = 'Composer setup finished.';
                exec($command, $output);
                //$output[] = '';

                if (is_readable(HOME_DIR . 'composer-setup.php')) {
                    // php -r "unlink('composer-setup.php');"
                    echo 'Unlink "composer-setup.php"...' . "\n";
                    unlink(HOME_DIR . 'composer-setup.php');
                    echo SPLITTER;
                }

            } else {
                echo 'Unlink "composer-setup.php". Installer corrupt!' . "\n";
                unlink('composer-setup.php');
                echo SPLITTER;
                exit ();
            }
        }

        if (is_readable(HOME_DIR . 'composer-setup.php')) {
            echo 'Unlink "composer-setup.php" second time!' . "\n";
            unlink(HOME_DIR . 'composer-setup.php');
            echo SPLITTER;
        }

        if (defined('OAUTH_TOKEN')) {
            exec('php ' . HOME_DIR . 'composer.phar config -g github-oauth.github.com ' . OAUTH_TOKEN, $output);
        }

        echo 'Going on...' . "\n";
        echo SPLITTER;
        putenv('COMPOSER_DISCARD_CHANGES=true');
        $command = 'php ' . HOME_DIR . 'composer.phar install';
        $output[] = $command . '...';
        exec($command, $output);
        //$output[] = '';
        $command = 'php ' . HOME_DIR . 'composer.phar show';
        $output[] = $command . '...';
        exec($command, $output);

        echo SPLITTER;
        echo implode("\n", array_filter($output));
        echo "\n" . SPLITTER;

        // remove oauth token for security reasons every time
        if (defined('OAUTH_TOKEN')) {
            echo 'Unlink "auth.json"' . "\n";
            unlink(COMPOSER_HOME_DIR . 'auth.json');
            echo SPLITTER;
        }
        echo 'Done.';
    } else {
        echo SPLITTER;
        echo 'File of packages dependencies "composer.json" not found.' . "\n";
        echo 'File "composer.json" will creating automatically...';
        $fileLocation = HOME_DIR . "composer.json";
        $file = fopen($fileLocation, "w");
        $content = '{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/artiomsreda/cashincashout.git"
    }
  ],
  "minimum-stability": "dev",
  "require-dev": {
    "artiomsreda/cashincashout": "@dev"
  }
}';
        fwrite($file, $content);
        fclose($file);

        if (is_readable('composer.json')) {
            echo "\n\n" . 'File "composer.json" created successfully' . ".\n";
            echo SPLITTER;
            // script runs again
            argsHandler("Now we can run script again for dependencies file deploying. Type 'yes' to continue: ", "builder");
        } else {
            echo SPLITTER;
            echo "\n\n" . 'Something goes wrong!!!';
        }

    }
}

function argsHandler($message, $callback)
{
    if (empty($message)) {
        echo 'Message "arg" is empty' . "\n";
    }
    echo $message;
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    if (trim($line) != 'yes') {
        echo "ABORTING!\n";
        exit;
    } else {
        echo SPLITTER;
        echo "Script runs again...\n";
        call_user_func($callback, '');
    }
    fclose($handle);
}

