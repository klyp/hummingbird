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
            $content = file_get_contents('.env-example');
            $io = $event->getIO();
            $confirmation = false;

            while (! $confirmation) {
                $acfKey = $io->ask('Please enter ACF plugin key: ');
                $confirmation = $io->askConfirmation('Are you sure(y/n)? ');
            }

            // insert acf key
            $content = str_replace('{ACF_KEY}' , $acfKey, $content);

            // update .env with acf key
            $result = file_put_contents('.env', $content);

            if ($result) {
                echo 'ACF Key Updated.' . "\n";
            } else {
                echo 'Something went wrong, please try again.' . "\n";
            }
        } else {
            echo '.env file exists, skipping...' . "\n";
        }
    }
}