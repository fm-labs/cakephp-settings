<?php
use Cake\Core\Configure;
use Settings\Configure\Engine\SettingsConfig;

if (!Cake\Core\Configure::configured('settings')) {
    Configure::config('settings', new SettingsConfig());
}

//@TODO Cache configuration
