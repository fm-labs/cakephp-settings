<?php
declare(strict_types=1);

namespace Settings;

use Banana\Menu\Menu;
use Banana\Plugin\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;
use Settings\Configure\Engine\SettingsConfig;

class Plugin extends BasePlugin implements EventListenerInterface
{
    /**
     * {@inheritDoc}
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        if (!\Cake\Cache\Cache::getConfig('settings')) {
            \Cake\Cache\Cache::setConfig('settings', [
                'className' => 'File',
                'duration' => Configure::read('debug') ? '+5 minutes' : '+ 999 days',
                'path' => CACHE,
                'prefix' => 'settings_',
            ]);
        }

        if (!\Cake\Log\Log::getConfig('settings')) {
            \Cake\Log\Log::setConfig('settings', [
                'className' => 'Cake\Log\Engine\FileLog',
                'path' => LOGS,
                'file' => 'settings',
                //'levels' => ['notice', 'info', 'debug'],
                'scopes' => ['settings'],
            ]);
        }

        Configure::config('settings', new SettingsConfig(Configure::read('Settings.modelName')));
        Configure::load('default', 'settings');
        Configure::load('global', 'settings');

        EventManager::instance()->on($this);
    }

    /**
     * {@inheritDoc}
     */
    public function routes(RouteBuilder $routes): void
    {
        $routes->scope('/admin/settings', ['prefix' => 'Admin', 'plugin' => 'Settings'], function ($routes) {
            $routes->connect(
                '/manage/*',
                ['controller' => 'SettingsManager', 'action' => 'manage'],
                ['_name' => 'settings:manage']
            );
            $routes->fallbacks(DashedRoute::class);
        });
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
            'Backend.Menu.build.admin_system' => ['callable' => 'buildBackendMenu', 'priority' => 90],
        ];
    }

    /**
     * @param \Cake\Event\Event $event The event object
     * @param \Banana\Menu\Menu $menu
     */
    public function buildBackendMenu(Event $event, Menu $menu)
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
