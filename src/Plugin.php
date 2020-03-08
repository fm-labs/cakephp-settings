<?php

namespace Settings;

use Banana\Application;
use Banana\Plugin\BasePlugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

class Plugin extends BasePlugin implements EventListenerInterface
{
    /**
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Backend.Menu.build.admin_system' => ['callable' => 'buildBackendMenu', 'priority' => 90],
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
            'children' => $children,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function bootstrap(PluginApplicationInterface $app)
    {
        parent::bootstrap($app);

        EventManager::instance()->on($this);
    }

    public function routes($routes)
    {
        $routes->scope('/admin/settings', ['prefix' => 'admin', 'plugin' => 'Settings'], function ($routes) {
            $routes->fallbacks(DashedRoute::class);
        });
    }
}
