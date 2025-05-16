<?php
declare(strict_types=1);

namespace Settings\Settings;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Utility\Hash;
use Exception;

/**
 * Class SettingsManager
 *
 * @package Settings
 */
class SettingsManager
{
    /**
     * @var \Settings\Settings\SettingsSchema;
     */
    protected $_schema;

    /**
     * @var array
     */
    protected array $_values = [];

    /**
     * @var array
     */
    protected array $_compiled = [];

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * @return \Settings\Settings\SettingsSchema
     */
    public function getSchema(): SettingsSchema
    {
        if (!$this->_schema) {
            $this->_schema = new SettingsSchema();
        }

        return $this->_schema;
    }

    /**
     * Load a settings config file
     *
     * @param string $file File name without file extension
     * @return void
     */
    public function load(string $file = 'settings'): void
    {
        [$plugin, $file] = pluginSplit($file);
        $plugin = $plugin && strtolower($plugin) != 'app' ? $plugin : null;
        $path = $plugin ? Plugin::configPath($plugin) : CONFIG;
        $filepath = $path . $file . '.php';
        $reader = function ($path) {
            if (!file_exists($path)) {
                return false;
            }

            $content = include $path;

            return $content;
        };

        $settings = $reader($filepath);
        if (is_array($settings) && isset($settings['Settings'])) {
            foreach ($settings['Settings'] as $pluginName => $pluginSettings) {
                $groups = $pluginSettings['groups'] ?? [];
                foreach ($groups as $group => $groupSettings) {
                    $this->getSchema()->addGroup($group, $groupSettings);
                }
                $schema = $pluginSettings['schema'] ?? [];
                foreach ($schema as $key => $settingConfig) {
                    $this->getSchema()->add($key, $settingConfig);
                }
            }
        }
    }

    /**
     * Attempt to autoload a settings schema file for each loaded plugin.
     *
     * @return void
     */
    public function autoload(): void
    {
        // load app settings
        try {
            $this->load('settings');
        } catch (Exception $e) {
        }

        // load plugin settings
        foreach (Plugin::loaded() as $plugin) {
            $path = sprintf('%s.%s', $plugin, 'settings');
            try {
                $this->load($path);
            } catch (Exception $e) {
            }
        }
    }

    /**
     * Get value
     *
     * @param string $key Setting key
     * @return string|null
     */
    public function getValue(string $key): ?string
    {
        return $this->_values[$key] ?? null;
    }

    /**
     * Apply values
     *
     * @param array $values Settings values
     * @return $this
     */
    public function apply(array $values = [])
    {
        $values = Hash::flatten($values);
        foreach ($values as $key => $val) {
            $this->_values[$key] = $val;
        }
        $this->_compiled = [];

        return $this;
    }

    /**
     * @return array
     */
    public function getCompiled(): array
    {
        if (!empty($this->_compiled)) {
            return $this->_compiled;
        }

        $compiled = [];
        foreach ($this->getSchema()->getSettings() as $key => $config) {
            $value = $this->_values[$key] ?? null;
            $compiled[$key] = $value;
        }

        return $this->_compiled = $compiled;
    }

    /**
     * Get the key-value pairs from the current app configuration.
     *
     * @return array
     */
    public function getCurrentConfig(): array
    {
        $values = [];
        foreach (array_keys($this->getSchema()->getSettings()) as $settingKey) {
            $value = Configure::read($settingKey);
            if ($value) {
                $values[$settingKey] = $value;
            }
        }

        return $values;
    }

    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        return [
            'schema' => $this->getSchema(),
            'values' => $this->_values,
            'compiled' => $this->getCompiled(),
        ];
    }
}
