<?php

namespace Settings;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Utility\Hash;

/**
 * Class SettingsManager
 * @package Settings
 */
class SettingsManager
{
    protected static $_builders = [];

    /**
     * @var array
     */
    protected $_groups = [];

    /**
     * @var array
     */
    protected $_settings = [];

    /**
     * @var array
     */
    protected $_values = [];

    /**
     * @var array
     */
    protected $_compiled = [];

    public static function register($scope, callable $settingsBuilder)
    {
        self::$_builders[$scope] = $settingsBuilder;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Get value
     *
     * @param string $key Setting key
     * @return null
     */
    public function value($key)
    {
        return (isset($this->_values[$key])) ? $this->_values[$key] : null;
    }

    /**
     * Load a settings config file
     *
     * @param string $file File name without file extension
     * @return void
     */
    public function load($file = 'settings')
    {
        list($plugin, $file) = pluginSplit($file);
        $path = ($plugin) ? Plugin::configPath($plugin) : CONFIG;
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
            foreach ($settings['Settings'] as $group => $groupSettings) {
                $this->addGroup($group, $groupSettings);
            }
        }
    }

    /**
     * @param string $group Setting group alias
     * @param array $config Setting group config
     * @return $this
     * @deprecated Grouping has been deprecated - Use push() method instead
     */
    public function addGroup($group, array $config = [])
    {
        $config += ['label' => null];
        $settings = [];

        if (isset($config['settings'])) {
            $settings = $config['settings'];
            unset($config['settings']);
        }
        /*
        if (!isset($this->_settings[$group])) {
            $this->_settings[$group] = $config;
        }
        */
        $this->add($group, $settings);

        return $this;
    }

    /**
     * @param string $group Setting group alias
     * @param string $key Setting key
     * @param array $config Setting schema
     * @return $this
     * @deprecated Grouping has been deprecated - Use push() method instead
     */
    public function add($group, $key, array $config = [])
    {
        if (is_array($key)) {
            foreach ($key as $_key => $_config) {
                $this->add($group, $_key, $_config);
            }

            return $this;
        }

        $scope = $group;
        if (strpos($group, ".")) {
            list($scope,) = pluginSplit($group);
        }

        $config['group'] = $group;
        $config['scope'] = $scope;
        $this->push($key, $config);

        return $this;
    }

    public function push($key, $config)
    {
        if (is_array($key)) {
            foreach ($key as $_key => $_config) {
                $this->push($_key, $_config);
            }

            return $this;
        }

        $config['key'] = $key;
        $this->_settings[$key] = $config;
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
    public function getSettings()
    {
        return $this->_settings;
    }

    /**
     * @return array
     */
    public function getCompiled()
    {
        if (!empty($this->_compiled)) {
            return $this->_compiled;
        }

        $compiled = [];
        /*
        foreach ($this->_settings as $group => $settings) {
            foreach ($settings['settings'] as $key => $config) {
                $value = (isset($this->_values[$key])) ? $this->_values[$key] : null;

                $compiled[$key] = $value;
            }
        }
        */
        foreach ($this->_settings as $key => $config) {
            $value = (isset($this->_values[$key])) ? $this->_values[$key] : null;

            $compiled[$key] = $value;
        }

        return $this->_compiled = $compiled;
    }

    public function __debugInfo()
    {
        return [
            'settings' => $this->_settings,
            'compiled' => $this->getCompiled()
        ];
    }
}
