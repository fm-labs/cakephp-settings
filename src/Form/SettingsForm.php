<?php

namespace Settings\Form;

use Cake\Core\Configure;
use Cake\Form\Form;
use Cake\Form\Schema;
use Settings\SettingsManager;

/**
 * Class SettingsForm
 * @package Settings\Form
 */
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

    /**
    /**
     * @return SettingsManager
     */
    public function manager()
    {
        if (!$this->_manager) {
            $this->_manager = new SettingsManager();
        }
        return $this->_manager;
    }

    /**
     * @param Schema|null $schema
     * @return Schema
     */
    public function schema(Schema $schema = null)
    {
        return parent::schema($schema);
    }

    /**
     * @param Schema $schema
     * @return Schema
     */
    protected function _buildSchema(Schema $schema)
    {
        return $this->manager()->buildFormSchema($schema);
    }

    /**
     * @param array $inputs
     * @return array
     */
    public function inputs($inputs = [])
    {
        if (!empty($inputs)) {
            return $this->_inputs = $inputs;
        }

        if (empty($this->_inputs)) {
            $this->_inputs = $this->manager()->buildFormInputs();
        }
        return $this->_inputs;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function value($key)
    {
        $value = $this->manager()->value($key);
        if ($value === null && Configure::check($key)) {
            $value = Configure::read($key);
        }
        return $value;
    }

    /**
     * @param array $data
     * @return $this|bool
     */
    public function execute(array $data = [])
    {
        return $this->manager()->apply($data);
    }
}
