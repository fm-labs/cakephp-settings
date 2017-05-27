<?php

namespace Settings;


use Cake\Event\Event;
use Cake\Event\EventDispatcherInterface;
use Cake\Event\EventManager;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class SettingsManager implements EventDispatcherInterface
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

    public function __construct()
    {

    }

    protected function _loadSettings()
    {
        if (!empty($this->_settings)) {
            return;
        }

        // a) read settings.php from each plugin

        // b) collect settings definitions with event
        $event = $this->dispatchEvent('Settings.get');
        $this->_settings = (array) $event->result;
    }

    protected function _loadValues()
    {
        if (!empty($this->_values)) {
            return;
        }

        $settings = TableRegistry::get('Settings.Settings')->find()->all();
        $values = [];

        foreach ($settings as $setting) {
            $namespace = $setting->scope;
            $fieldKey = $namespace . '.' . $setting->key;
            $values[$fieldKey] = $setting->value;
        }

        $this->_values = $values;
    }

    public function value($key)
    {
        $this->_loadValues();
        return (isset($this->_values[$key])) ? $this->_values[$key] : null;
    }

    public function getSettings()
    {
        $this->_loadSettings();
        return $this->_settings;
    }

    public function getCompiled()
    {
        if (!empty($this->_compiled)) {
            return $this->_compiled;
        }

        $this->_loadSettings();
        $this->_loadValues();


    }

    /**
     * @param array $data
     */
    public function apply(array $data = [])
    {
        $data = Hash::flatten($data);
        foreach ($data as $key => $val)
        {
            $this->_values[$key] = $val;
        }
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