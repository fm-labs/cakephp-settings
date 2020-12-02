<?php
declare(strict_types=1);

namespace Settings;

use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;
use Settings\Configure\Engine\SettingsConfig;

class Plugin extends BasePlugin
{
    /**
     * @inheritDoc
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        if (!\Cake\Cache\Cache::getConfig('settings')) {
            \Cake\Cache\Cache::setConfig('settings', [
                'className' => 'File',
                'duration' => Configure::read('debug') ? '+5 minutes' : '+ 999 days',
                'path' => CACHE,
                'prefix' => 'settings_',
            ]);
        }

        if (!\Cake\Log\Log::getConfig('settings')) {
            \Cake\Log\Log::setConfig('settings', [
                'className' => 'Cake\Log\Engine\FileLog',
                'path' => LOGS,
                'file' => 'settings',
                //'levels' => ['notice', 'info', 'debug'],
                'scopes' => ['settings'],
            ]);
        }

        try {
            Configure::config('settings', new SettingsConfig(Configure::read('Settings.modelName'), 'plugin'));
            //Configure::load('App:default', 'settings');
        } catch (\Exception $ex) {
            die($ex->getMessage());
        }

        if (\Cake\Core\Plugin::isLoaded('Admin')) {
            \Admin\Admin::addPlugin(new \Settings\Admin());
        }
    }
}
