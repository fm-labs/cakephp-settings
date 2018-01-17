<?php

namespace Settings;

use Cake\Core\Configure;
use Cake\Event\EventListenerInterface;
use Cake\Form\Schema;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\Utility\Text;

/**
 * Class SettingsManager
 * @package Settings
 */
class SettingsManager implements EventListenerInterface
{
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


    public function implementedEvents()
    {
        return ['Banana.init' => 'init'];
    }

    public function init()
    {
        //debug("SettingsManager init");
    }

    public function describe()
    {
        $schema = $this->buildFormSchema(new Schema());
        $inputs = $this->buildFormInputs();

        $result = [];
        foreach ($this->_settings as $namespace => $_settings) {
            foreach (array_keys($_settings) as $key) {
                $fieldKey = $namespace . '.' . $key;
                $result[$namespace][$key] = [
                    'field' => $schema->field($fieldKey),
                    'input' => $inputs[$fieldKey]
                ];
            }
        }
        return $result;
    }

    /**
     * @param Schema $schema
     * @return Schema
     */
    public function buildFormSchema(Schema $schema)
    {
        foreach ($this->_settings as $namespace => $settings) {
            foreach ($settings as $key => $config) {
                $columnConfig = array_diff_key($config, ['inputType' => null, 'input' => null, 'default' => null]);
                $key = $namespace . '.' . $key;
                $schema->addField($key, $columnConfig);
            }
        }

        return $schema;
    }

    /**
     * @return array
     */
    public function buildFormInputs()
    {
        $inputs = [];
        /*
        $autoInputType = function($dataType) {
            switch ($dataType) {
                case 'text':
                    return 'textarea';

                case 'string':
                default:
                    return 'text';
            }
        };
        */
        foreach ($this->_settings as $namespace => $settings) {
            foreach ($settings as $key => $config) {

                $fieldKey = $namespace . '.' . $key;
                $config += ['input' => [], 'default' => null, 'type' => null];

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

                $defaultInput = [
                    'type' => null,
                    'label' => $label,
                    'default' => $config['default'],
                    'value' => ($this->value($fieldKey)) ?: Configure::read($fieldKey)
                ];
                $input = array_merge($defaultInput, $input);
                $input = $this->_buildInput($input, $config);

                $inputs[$fieldKey] = $input;
            }
        }
        return $inputs;
    }

    protected function _buildInput(array $input, array $config = [])
    {

        if (!$input['type']) {
            switch($config['type']) {
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

    protected function _mapTypeToInputType($type)
    {

    }

    /**
     * Get value
     *
     * @param $key
     * @return null
     */
    public function value($key)
    {
        return (isset($this->_values[$key])) ? $this->_values[$key] : null;
    }

    public function add($namespace, $key, array $config = [])
    {
        if (is_array($key)) {
            foreach ($key as $_key => $_config) {
                $this->add($namespace, $_key, $_config);
            }
            return $this;
        }
        $this->_settings[$namespace][$key] = $config;
        return $this;
    }

    /**
     * Apply values
     *
     * @param array $values
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
        foreach ($this->_settings as $namespace => $settings) {
            foreach ($settings as $setting => $config) {
                $key = $namespace . '.' . $setting;
                $value = (isset($this->_values[$key])) ? $this->_values[$key] : null;

                $compiled[$key] = $value;
            }
        }

        return $this->_compiled = $compiled;
    }


}
