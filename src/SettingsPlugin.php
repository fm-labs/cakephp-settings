<?php

namespace Settings;

use Backend\Backend;
use Backend\BackendPluginInterface;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\Routing\RouteBuilder;

class SettingsPlugin implements BackendPluginInterface, SettingsInterface, EventListenerInterface
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
     * @param Event $event
     */
    public function buildBackendMenu(Event $event)
    {
        $event->subject()->addItem([
            'title' => 'Settings',
            'url' => ['plugin' => 'Settings', 'controller' => 'SettingsManager', 'action' => 'manage'],
            'data-icon' => 'sliders',
            'children' => [
//                'settings' => [
//                    'title' => __d('settings', 'Settings Table'),
//                    'url' => ['plugin' => 'Settings', 'controller' => 'Settings', 'action' => 'index'],
//                    'data-icon' => 'gears'
//                ],
//                'manager' => [
//                    'title' => __d('settings', 'Manager'),
//                    'url' => ['plugin' => 'Settings', 'controller' => 'SettingsManager', 'action' => 'index'],
//                    'data-icon' => 'gears'
//                ],
            ]
        ]);
    }
    
    public function buildSettings(SettingsManager $settings)
    {
        $settings->add('Settings', 'autoBackup', [
            'type' => 'boolean',
            'default' => false,
        ]);
    }

    public function backendBootstrap(Backend $backend)
    {
        EventManager::instance()->on($this);
    }

    public function backendRoutes(RouteBuilder $routes)
    {
        // Admin routes
        $routes->fallbacks('DashedRoute');
//        $routes->scope('/settings',
//            ['plugin' => 'Settings', 'prefix' => 'admin', '_namePrefix' => 'settings:admin:'],
//            function (RouteBuilder $routes) {
//                //$routes->connect('/:controller');
//                $routes->fallbacks('DashedRoute');
//            });
    }
}