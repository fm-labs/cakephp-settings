<?php

namespace Settings;

use Cake\Core\Configure;
//use Cake\Core\Configure\FileConfigTrait;
use Cake\Core\Plugin;
use Cake\Form\Schema;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\Utility\Text;

/**
 * Class SettingsManager
 * @package Settings
 */
class SettingsManager
{
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

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_schema = new Schema();
    }

    /**
     * @return array
     */
    public function describe()
    {
        $schema = $this->buildFormSchema($this->_schema);
        $inputs = $this->buildFormInputs();

        $result = [];
        /*
        foreach ($this->_settings as $group => $settings) {
            foreach ($settings['settings'] as $_key => $_setting) {
                $result[$group][$_key] = [
                    'field' => $schema->field($_key),
                    'input' => $inputs[$_key]
                ];
            }
        }
        */
        foreach ($this->_settings as $_key => $_setting) {
            $result[$_key] = [
                'key' => $_key,
                'field' => $schema->field($_key),
                'input' => $inputs[$_key],
            ];
        }

        return $result;
    }

    /**
     * @param Schema $schema Table schema
     * @return Schema
     */
    public function buildFormSchema(Schema $schema)
    {
        /*
        foreach ($this->_settings as $group => $settings) {
            foreach ($settings['settings'] as $key => $config) {
                $columnConfig = array_diff_key($config, ['inputType' => null, 'input' => null, 'default' => null]);
                $schema->addField($key, $columnConfig);
            }
        }
        */
        foreach ($this->_settings as $key => $config) {
            $columnConfig = array_diff_key($config, ['inputType' => null, 'input' => null, 'default' => null]);
            $schema->addField($key, $columnConfig);
        }

        return $schema;
    }

    protected function _buildFormInput($key, array $config = [])
    {
        $config += ['input' => [], 'default' => null, 'type' => null, 'desc' => null];

        $input = $config['input'];
        unset($config['input']);
        if (is_string($input)) {
            $input = ['type' => $input];
        }

        $label = Inflector::humanize(Text::slug($key, ' '));
        if (isset($config['label'])) {
            $label = $config['label'];
            unset($config['label']);
        }

        $desc = null;
        if (isset($config['desc'])) {
            $desc = $config['desc'];
            unset($config['desc']);
        }

        $defaultInput = [
            'type' => null,
            'label' => $label,
            'default' => $config['default'],
            'value' => $this->value($key), //($this->value($key)) ?: Configure::read($key),
            'help' => $desc,
        ];
        $input = array_merge($defaultInput, $input);
        $input = $this->_buildInput($input, $config);

        return $input;
    }

    /**
     * @return array
     */
    public function buildFormInputs()
    {
        $inputs = [];
        /*
        foreach ($this->_settings as $namespace => $settings) {
            foreach ($settings['settings'] as $key => $config) {
                $inputs[$key] = $this->_buildFormInput($key, $config);
            }
        }
        */
        foreach ($this->_settings as $key => $config) {
            $inputs[$key] = $this->_buildFormInput($key, $config);
        }

        return $inputs;
    }

    /**
     * @param array $input Input schema
     * @param array $config Input config
     * @return array Input schema
     */
    protected function _buildInput(array $input, array $config = [])
    {
        if (!$input['type']) {
            switch ($config['type']) {
                case "boolean":
                    $input['type'] = "checkbox";
                    $input['val'] = $input['value'];
                    $input['value'] = 1;
                    break;

                case "text":
                case "html":
                    $input['type'] = "textarea";
                    break;

                case "integer":
                case "double":
                case "decimal":
                    $input['type'] = "numeric";
                    break;

                case "string":
                    $input['type'] = "text";
                    break;

                default:
                    $input['type'] = $config['type'];
                    break;
            }
        }

        return $input;
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

        $config['group'] = $group;
        $this->push($key, $config);

        /*
        // -- OLD --
        if (!isset($this->_settings[$group])) {
            debug("Warning: Settings group does not exist: $group");
            $this->addGroup($group);
        }

        $this->_settings[$group]['settings'][$key] = $config;

        // -- OLDER --
        $columnConfig = array_diff_key($config, ['inputType' => null, 'input' => null, 'default' => null]);
        $this->_schema->addField($key, $columnConfig);

        $input = $this->_buildFormInput($key, $config);

        $setting = [
            'field' => $this->_schema->field($key),
            'input' => $input
        ];

        $this->_settings[$group]['settings'][$key] = $setting;
        */

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
}
