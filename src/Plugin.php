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
     * @var bool
     */
    public $routesEnabled = false;

    /**
     * @var bool
     */
    public $bootstrapEnabled = true;

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        if (!\Cake\Cache\Cache::getConfig('settings')) {
            \Cake\Cache\Cache::setConfig('settings', [
                'className' => 'File',
                'duration' => Configure::read('debug') ? '+5 minutes' : '+ 999 days',
                'path' => CACHE . 'settings' . DS,
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

        // Register config engine
        try {
            $engine = new SettingsConfig(Configure::read('Settings.modelName'), 'plugin');
            Configure::config('settings', $engine);
        } catch (\Exception $ex) {
            debug($ex->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        if (\Cake\Core\Plugin::isLoaded('Admin')) {
            \Admin\Admin::addPlugin(new \Settings\Admin());
        }
    }
}
