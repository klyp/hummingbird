<?php

namespace Klyp;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;

class Script
{
    public static function preInstall(Event $event)
    {
        // check if the env file exists
        if (! file_exists('.env')) {
            // create .env file
            $envContent = file_get_contents('.env-example');
            $io = $event->getIO();

            // start wp configurations
            $wpEnvs = array (
                        '{DB_NAME}',
                        '{DB_USER}',
                        '{DB_PASSWORD}',
                        '{DB_HOST}',
                        '{DB_PREFIX}',
                        '{DB_CHARSET}',
                        '{DB_COLLATE}',
                        '{WP_DEBUG}',
                        '{WP_DEBUG_DISPLAY}',
                        '{WP_DEBUG_LOG}',
                        '{ACF_KEY}',
                        '{AUTH_KEY}',
                        '{SECURE_AUTH_KEY}',
                        '{LOGGED_IN_KEY}',
                        '{NONCE_KEY}',
                        '{AUTH_SALT}',
                        '{SECURE_AUTH_SALT}',
                        '{LOGGED_IN_SALT}',
                        '{NONCE_SALT}'
                    );

            // database name
            $wpDbName = false;
            while (! $wpDbName) {
                $wpDbName = $io->ask('Please enter Database Name: ') ?: false;
            }
            $wpEnvReplace[] = $wpDbName;

            // database user
            $wpDbUser = false;
            while (! $wpDbUser) {
                $wpDbUser = $io->ask('Please enter Database Username: ') ?: false;
            }
            $wpEnvReplace[] = $wpDbUser;

            // database password
            $wpDbPassword = false;
            while (! $wpDbPassword) {
                $wpDbPassword = $io->askAndHideAnswer('Please enter Database Password: ') ?: false;
            }
            $wpEnvReplace[] = $wpDbPassword;

            // database host
            $wpDbHost = false;
            while (! $wpDbHost) {
                $wpDbHost = $io->ask('Please enter Database Host (default: localhost): ') ?: 'localhost';
            }
            $wpEnvReplace[] = $wpDbHost;

            // database prefix
            $wpDbPrefix = false;
            while (! $wpDbPrefix) {
                $wpDbPrefix = $io->ask('Please enter Database Prefix (eg. ' . getRandomString(8) . '_): ') ?: getRandomString(8) . '_';
            }
            $wpEnvReplace[] = $wpDbPrefix;

            // database charset
            $wpDbCharset = false;
            while (! $wpDbCharset) {
                $wpDbCharset = $io->ask('Please enter Database Charset (eg. utf8mb4): ') ?: 'utf8mb4';
            }
            $wpEnvReplace[] = $wpDbCharset;

            // database charset
            $wpDbGeneralCi = false;
            while (! $wpDbGeneralCi) {
                $wpDbGeneralCi = $io->ask('Please enter Database Collation (eg. utf8_general_ci): ') ?: 'utf8_general_ci';
            }
            $wpEnvReplace[] = $wpDbGeneralCi;

            // debug mode
            $wpEnvReplace[] = (int) $io->askConfirmation('Enable Debug Mode (y/n)? ');

            // display debug
            $wpEnvReplace[] = (int) $io->askConfirmation('Enable Display Debug (y/n)? ');

            // write debug
            $wpEnvReplace[] = (int) $io->askConfirmation('Enable Debug Logging (y/n)? ');

            // ACF Key
            $acfKey = false;
            while (! $acfKey) {
                $acfKey = $io->ask('Please enter ACF plugin key: ');
            }
            $wpEnvReplace[] = $acfKey;

            // generate wp secure key and salts
            $wpKeySalts = file_get_contents('https://api.wordpress.org/secret-key/1.1/salt/');
            $wpKeySalts = explode(PHP_EOL, $wpKeySalts);
            foreach ($wpKeySalts as $key => $wpKeySalt) {
                if ($wpKeySalt != '') {
                    $wpKeyValue = explode("',", $wpKeySalt);
                    $wpKeyValue = str_replace(');', '', trim($wpKeyValue[1]));
                    $wpEnvReplace[] = $wpKeyValue;
                }
            }

            // replace
            $envContent = str_replace($wpEnvs , $wpEnvReplace, $envContent);

            // update .env with acf key
            $result = file_put_contents('.env', $envContent);
            if ($result) {
                echo 'Installation completed.' . "\n";
            } else {
                echo 'Something went wrong, please try again.' . "\n";
            }
        } else {
            echo '.env file exists, skipping...' . "\n";
        }
    }
}
/**
* Function to generate random string.
* @param int
* @return string
*/
function getRandomString($length = 8)
{
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)))), 1, $length);
}
