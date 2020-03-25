<?php
declare(strict_types=1);

namespace Settings\Controller\Admin;

use Cake\Core\Configure;

class AppController extends \App\Controller\Admin\AppController
{
    public function initialize(): void
    {
        parent::initialize();

        $this->modelClass = Configure::read('Settings.modelName') ?: 'Settings.Settings';
    }
}
