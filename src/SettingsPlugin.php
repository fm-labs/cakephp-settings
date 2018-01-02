<?php

namespace Settings;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;

class SettingsPlugin implements EventListenerInterface
{
    /**
     * Returns a list of events this object is implementing. When the class is registered
     * in an event manager, each individual method will be associated with the respective event.
     *
     * @see EventListenerInterface::implementedEvents()
     * @return array associative array or event key names pointing to the function
     * that should be called in the object when the respective event is fired
     */
    public function implementedEvents()
    {
        return [
            'Backend.Menu.get' => ['callable' => 'getBackendMenu', 'priority' => 99 ],
        ];
    }

    /**
     * @param Event $event
     */
    public function getBackendMenu(Event $event)
    {
        $event->subject()->addItem([
            'title' => 'Settings',
            'url' => ['plugin' => 'Banana', 'controller' => 'Settings', 'action' => 'manage'],
            'data-icon' => 'sliders',
        ]);
    }

    public function __invoke()
    {
    }
}