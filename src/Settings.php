<?php
declare(strict_types=1);

namespace Settings;

use Cake\Core\Configure;

class Settings
{
//    /**
//     * @var \Settings\Settings\SettingsSchema
//     */
//    protected static $schema;
//
//    /**
//     * Load a settings schema file from config dir.
//     *
//     * @param string $path Path to settings file. e.g. 'settings' or 'PluginName.settings'
//     * @return void
//     * @throws \Exception
//     */
//    public static function load($path = 'settings'): void
//    {
//        [$plugin, $file] = pluginSplit($path);
//        $path = $plugin ? \Cake\Core\Plugin::configPath($plugin) : CONFIG;
//        $filepath = $path . $file . '.php';
//        $reader = function ($path) {
//            if (!file_exists($path)) {
//                throw new \Exception('Settings file not found in path: ' . $path);
//                //return [];
//            }
//
//            return include $path;
//        };
//
//        $settings = $reader($filepath);
//        if (is_array($settings) && isset($settings['Settings'])) {
//            foreach ($settings['Settings'] as $scope => $scopeSettings) {
//                $groups = $scopeSettings['groups'] ?? [];
//                foreach ($groups as $group => $groupSettings) {
//                    static::schema()->addGroup($group, $groupSettings);
//                }
//                $schema = $scopeSettings['schema'] ?? [];
//                foreach ($schema as $key => $settingConfig) {
//                    static::schema()->add($scope, $key, $settingConfig);
//                }
//            }
//        }
//    }
//
//    /**
//     * Attempt to autoload a settings schema file for each bootstrap-enabled plugin
//     * @param \Cake\Core\PluginApplicationInterface $app
//     * @return void
//     */
//    public static function autoload(PluginApplicationInterface $app): void
//    {
//        // load app settings
//        try {
//            static::load('settings');
//        } catch (\Exception $e) {
//        }
//
//        // load plugin settings
//        foreach ($app->getPlugins()->with('bootstrap') as $plugin) {
//            $path = sprintf('%s.%s', $plugin->getName(), 'settings');
//            try {
//                static::load($path);
//            } catch (\Exception $e) {
//            }
//        }
//    }

    /**
     * Dump current config with settings engine.
     *
     * @param string $name Dump name
     * @param array $keys Top-level configuration keys to dump
     * @return bool True, if dump was successful
     */
    public static function dumpConfig(string $name, array $keys = []): bool
    {
        return Configure::dump($name, 'settings', $keys);
    }

    public static function clearCache(string $string, $plugin): void
    {
    }
}
