<?php

namespace Settings;

use Backend\Backend;
use Backend\BackendPluginInterface;
use Banana\Banana;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\Routing\RouteBuilder;

class SettingsPlugin implements BackendPluginInterface, EventListenerInterface
{
    /**
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Backend.Sidebar.build' => ['callable' => 'buildBackendMenu', 'priority' => 90]
        ];
    }

    /**
     * @param Event $event The event object
     * @return void
     */
    public function buildBackendMenu(Event $event)
    {
        $children = [];
        $event->subject()->addItem([
            'title' => 'Settings',
            'url' => ['plugin' => 'Settings', 'controller' => 'SettingsManager', 'action' => 'manage'],
            'data-icon' => 'sliders',
            'children' => $children
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function backendBootstrap(Backend $backend)
    {
        EventManager::instance()->on($this);
    }

    /**
     * {@inheritDoc}
     */
    public function backendRoutes(RouteBuilder $routes)
    {
        // Admin routes
        $routes->fallbacks('DashedRoute');
    }
}
