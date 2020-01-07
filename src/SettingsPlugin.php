<?php

namespace Settings;

use Banana\Application;
use Banana\Plugin\BasePlugin;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\Routing\RouteBuilder;

class SettingsPlugin extends BasePlugin implements EventListenerInterface
{
    protected $_name = "Settings";

    /**
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Backend.Menu.build.admin_system' => ['callable' => 'buildBackendMenu', 'priority' => 90]
        ];
    }

    /**
     * @param Event $event The event object
     * @return void
     */
    public function buildBackendMenu(Event $event, \Banana\Menu\Menu $menu)
    {
        $children = [];
        $menu->addItem([
            'title' => 'Settings',
            'url' => ['plugin' => 'Settings', 'controller' => 'SettingsManager', 'action' => 'manage'],
            'data-icon' => 'sliders',
            'children' => $children
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function bootstrap(Application $app)
    {
        parent::bootstrap($app);
        EventManager::instance()->on($this);
    }

    public function backendRoutes(RouteBuilder $routes)
    {
        $routes->fallbacks('DashedRoute');
    }
}
