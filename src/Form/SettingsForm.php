<?php

namespace Settings\Form;


use Cake\Form\Form;
use Cake\Form\Schema;
use Settings\SettingsManager;

class SettingsForm extends Form
{
    /**
     * @var SettingsManager
     */
    protected $_manager;

    /**
     * @var array
     */
    protected $_inputs = [];

    public function __construct()
    {
        $this->_manager = new SettingsManager();
    }

    public function schema(Schema $schema = null)
    {
        return parent::schema($schema);
    }

    protected function _buildSchema(Schema $schema)
    {
        foreach ($this->_manager->getSettings() as $namespace => $settings) {
            foreach ($settings as $key => $config) {
                $columnConfig = array_diff_key($config, ['inputType' => null, 'input' => null, 'default' => null]);
                $fieldKey = $namespace . '.' . $key;
                $schema->addField($fieldKey, $columnConfig);
            }
        }

        return $schema;
    }

    public function inputs($inputs = [])
    {
        if (!empty($inputs)) {
            return $this->_inputs = $inputs;
        }

        if (empty($this->_inputs)) {
            $inputs = [];
            foreach ($this->_manager->getSettings() as $namespace => $settings) {
                foreach ($settings as $key => $config) {

                    $inputType = (isset($config['inputType'])) ? $config['inputType'] : null;
                    $defaultValue = (isset($config['default'])) ? $config['default'] : null;
                    $fieldKey = $namespace . '.' . $key;
                    $inputConfig = [
                        'type' => $inputType,
                        'label' => $namespace . '.' . $key,
                        'default' => $defaultValue,
                    ];
                    if (isset($config['input'])) {
                        $inputConfig = array_merge($inputConfig, $config['input']);
                    }

                    $inputs[$fieldKey] = $inputConfig;
                }
            }
            $this->_inputs = $inputs;
        }

        return $this->_inputs;
    }

    public function value($key)
    {
        return $this->_manager->value($key);
    }

    public function execute(array $data = [])
    {
        $this->_manager->apply($data);
    }
}