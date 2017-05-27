<?php

namespace Settings\Form;


use Cake\Core\Configure;
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

    public function manager()
    {
        return $this->_manager;
    }

    public function schema(Schema $schema = null)
    {
        return parent::schema($schema);
    }

    protected function _buildSchema(Schema $schema)
    {
        return $this->_manager->buildFormSchema($schema);
    }

    public function inputs($inputs = [])
    {
        if (!empty($inputs)) {
            return $this->_inputs = $inputs;
        }

        if (empty($this->_inputs)) {
            $this->_inputs = $this->_manager->buildFormInputs();
        }
        return $this->_inputs;
    }

    public function value($key)
    {
        $value = $this->_manager->value($key);
        if ($value === null && Configure::check($key)) {
            $value = Configure::read($key);
        }
        return $value;
    }

    public function execute(array $data = [])
    {
        $this->_manager->apply($data);
    }
}