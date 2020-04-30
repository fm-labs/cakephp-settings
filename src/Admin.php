<?php
declare(strict_types=1);

namespace Settings;

use Admin\Core\BaseAdminPlugin;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;
use Cupcake\Menu\Menu;

class Admin extends BaseAdminPlugin implements EventListenerInterface
{
    /**
     * {@inheritDoc}
     */
    public function routes(RouteBuilder $routes): void
    {
        $routes->connect(
            '/manage/*',
            ['controller' => 'SettingsManager', 'action' => 'manage'],
            ['_name' => 'settings:manage']
        );
        $routes->fallbacks(DashedRoute::class);
    }

    /**
     * @return array|null
     */
    public function getConfigurationUrl()
    {
        return ['_name' => 'settings:manage', $this->getName()];
    }

    /**
     * {@inheritDoc}
     */
    public function implementedEvents(): array
    {
        return [
            'Admin.Menu.build.admin_system' => ['callable' => 'buildAdminMenu', 'priority' => 90],
        ];
    }

    /**
     * @param \Cake\Event\Event $event The event object
     * @param \Cupcake\Menu\Menu $menu The menu
     * @return void
     */
    public function buildAdminMenu(Event $event, Menu $menu): void
    {
        $children = [];
        $menu->addItem([
            'title' => 'Settings',
            'url' => ['plugin' => 'Settings', 'controller' => 'SettingsManager', 'action' => 'manage'],
            'data-icon' => 'sliders',
            'children' => $children,
        ]);
    }
}
