<?php

use Cake\Core\Configure;

defined('SETTINGS') || define('SETTINGS', CONFIG);
defined('SETTINGS_SCOPE') || define('SETTINGS_SCOPE', 'global');

if (!\Cake\Cache\Cache::config('settings')) {
    \Cake\Cache\Cache::config('settings', [
        'className' => 'File',
        'duration' => (Configure::read('debug')) ? '+5 minutes' : '+ 999 days',
        'path' => CACHE,
        'prefix' => 'settings_'
    ]);
}

if (!\Cake\Log\Log::config('settings')) {
    \Cake\Log\Log::config('settings', [
        'className' => 'Cake\Log\Engine\FileLog',
        'path' => LOGS,
        'file' => 'settings',
        //'levels' => ['notice', 'info', 'debug'],
        'scopes' => ['settings']
    ]);
}

Configure::config('settings', new \Settings\Configure\Engine\SettingsConfig(Configure::read('Settings.modelName')));
Configure::load('default', 'settings');
Configure::load(SETTINGS_SCOPE, 'settings');
