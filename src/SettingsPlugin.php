<?php
declare(strict_types=1);

namespace Settings;

use Admin\Admin;
use Cake\Cache\Cache;
use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Log\Log;
use Exception;
use Settings\Configure\Engine\SettingsConfig;

class SettingsPlugin extends BasePlugin
{
    //    /**
    //     * @var bool
    //     */
    //    public $routesEnabled = false;
    //
    //    /**
    //     * @var bool
    //     */
    //    public $bootstrapEnabled = true;

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        if (!Cache::getConfig('settings')) {
            Cache::setConfig('settings', [
                'className' => 'File',
                'duration' => Configure::read('debug') ? '+5 minutes' : '+ 999 days',
                'path' => CACHE . 'settings' . DS,
                'prefix' => 'settings_',
            ]);
        }

        if (!Log::getConfig('settings')) {
            Log::setConfig('settings', [
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
        } catch (Exception $ex) {
            debug($ex->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        if (Plugin::isLoaded('Admin')) {
            Admin::addPlugin(new SettingsAdmin());
        }
    }
}
