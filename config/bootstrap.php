<?php

if (!defined('SETTINGS')) {
    define('SETTINGS', CONFIG);
}
\Cake\Core\Configure::config('settings', new \Settings\Configure\Engine\SettingsConfig());