<?php

use Cake\Core\Configure;

if (!defined('SETTINGS')) {
    define('SETTINGS', CONFIG);
}

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

Configure::config('settings', new \Settings\Configure\Engine\SettingsConfig(Configure::read('Settings.modelClass')));
foreach ((array) Configure::read('Settings.autoload') as $scope) {
    Configure::load($scope, 'settings');
}