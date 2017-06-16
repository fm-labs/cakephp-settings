<?php

namespace Settings;

use Cake\Event\Event;
use Cake\Event\EventDispatcherInterface;
use Cake\Event\EventManager;
use Cake\Form\Schema;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Class SettingsManager
 * @package Settings
 */
class SettingsManager implements EventDispatcherInterface
{
    /**
     * @var string
     */
    protected $_scope;

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
     * @param array $settings
     * @param string $scope
     */
    public function __construct($scope = 'default', $settings = [])
    {
        $this->_scope = $scope;
        $this->_settings = $settings;

        if (!$this->_scope) {
            throw new \InvalidArgumentException("SettingsManager: Scope can not be empty");
        }
    }

    /**
     * Load available setting definitions
     */
    protected function _loadSettings()
    {
        if (!empty($this->_settings)) {
            return;
        }

        // a) read settings.php from each plugin

        // b) collect settings definitions with event
        $event = $this->dispatchEvent('Settings.get');
        $this->_settings = (array)$event->result;
    }

    /**
     * Load settings values from persistent storage
     */
    protected function _loadValues()
    {
        if (!empty($this->_values)) {
            return;
        }

        $values = [];
        $settings = TableRegistry::get('Settings.Settings')
            ->find()
            ->where(['Settings.scope' => $this->_scope])
            ->all();

        foreach ($settings as $setting) {
            $values[$setting->key] = $setting->value;
        }

        $this->_values = $values;
    }

    /**
     * @param Schema $schema
     * @return Schema
     */
    public function buildFormSchema(Schema $schema)
    {
        $this->_loadSettings();
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
        $this->_loadSettings();
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
                $config += ['input' => [], 'default' => null];

                $input = $config['input'];
                unset($config['input']);
                if (is_string($input)) {
                    $input = ['type' => $input];
                }

                $defaultInput = [
                    'type' => null,
                    'label' => $namespace . '.' . $key,
                    'default' => $config['default'],
                ];
                $input = array_merge($defaultInput, $input);
                $inputs[$fieldKey] = $input;
            }
        }
        return $inputs;
    }

    /**
     * @param $key
     * @return null
     */
    public function value($key)
    {
        $this->_loadValues();

        return (isset($this->_values[$key])) ? $this->_values[$key] : null;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        $this->_loadSettings();

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

        $this->_loadSettings();
        $this->_loadValues();

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

    /**
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
     * @return int
     * @TODO Refactor using config engine
     */
    public function dump()
    {
        $path = SETTINGS . 'settings_' . $this->_scope . '.php';
        $contents = '<?php' . "\n" . 'return ' . var_export($this->getCompiled(), true) . ';';

        return file_put_contents($path, $contents);
    }

    /**
     * Wrapper for creating and dispatching events.
     *
     * Returns a dispatched event.
     *
     * @param string $name Name of the event.
     * @param array|null $data Any value you wish to be transported with this event to
     * it can be read by listeners.
     * @param object|null $subject The object that this event applies to
     * ($this by default).
     *
     * @return \Cake\Event\Event
     */
    public function dispatchEvent($name, $data = null, $subject = null)
    {
        return $this->eventManager()->dispatch(new Event($name, $this, $data));
    }

    /**
     * Returns the global Cake\Event\EventManager manager instance.
     *
     * @param \Cake\Event\EventManager|null $eventManager the eventManager to set
     * @return \Cake\Event\EventManager
     */
    public function eventManager(EventManager $eventManager = null)
    {
        return EventManager::instance();
    }
}
