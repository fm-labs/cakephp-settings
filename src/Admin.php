<?php
declare(strict_types=1);

namespace Settings;

use Admin\Core\BaseAdminPlugin;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;
use Cupcake\Menu\MenuItemCollection;

class Admin extends BaseAdminPlugin implements EventListenerInterface
{
    /**
     * @inheritDoc
     */
    public function routes(RouteBuilder $routes): void
    {
        $routes->connect(
            '/',
            ['controller' => 'Settings', 'action' => 'index'],
            ['_name' => 'index']
        );
        $routes->connect(
            '/manage/{scope}/{pluginName}',
            ['controller' => 'Settings', 'action' => 'index'],
            ['_name' => 'manage', 'pass' => ['scope', 'pluginName']]
        );
        $routes->fallbacks(DashedRoute::class);
    }

    /**
     * @inheritDoc
     */
    public function implementedEvents(): array
    {
        return [
            'Admin.Menu.build.admin_system' => ['callable' => 'adminMenuBuild', 'priority' => 90],
        ];
    }

    /**
     * @param \Cake\Event\Event $event The event object
     * @param \Cupcake\Menu\MenuItemCollection $menu The menu
     * @return void
     */
    public function adminMenuBuild(Event $event, MenuItemCollection $menu): void
    {
        $children = [];
        $menu->addItem([
            'title' => 'Settings',
            'url' => ['_name' => 'admin:settings:index'],
            'data-icon' => 'sliders',
            'children' => $children,
        ]);
    }
}
