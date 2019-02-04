<?php

namespace Settings;

use Backend\Backend;
use Backend\BackendPluginInterface;
use Banana\Banana;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\Routing\RouteBuilder;
use Cake\Utility\Inflector;

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
        $children = [];
        foreach(Banana::getInstance()->plugins()->loaded() as $pluginName) {
            $instance = Banana::getInstance()->plugins()->get($pluginName);
            if ($instance instanceof SettingsInterface && $pluginName != "Settings") {
                 $children['plugin_' . $pluginName] = [
                    'title' => Inflector::humanize($pluginName),
                    'url' => ['plugin' => 'Settings', 'controller' => 'SettingsManager', 'action' => 'manage', 'namespace' => $pluginName],
                    'data-icon' => null
                ];
            }
        }

        $event->subject()->addItem([
            'title' => 'Settings',
            'url' => ['plugin' => 'Settings', 'controller' => 'SettingsManager', 'action' => 'manage'],
            'data-icon' => 'sliders',
            'children' => $children
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