<?php
use Cake\Core\Configure;
use Cake\Log\Log;
use Settings\Configure\Engine\SettingsConfig;

if (!Cake\Core\Configure::configured('settings')) {

    try {
        Configure::config('settings', new SettingsConfig());
        //Configure::load('settings', 'settings', true);
    } catch (\Exception $e) {

        //Log::error('Settings: ' . $e->getMessage());
        //die($e->getMessage() . "\n");
    }

}

