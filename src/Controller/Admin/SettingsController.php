<?php
declare(strict_types=1);

namespace Settings\Controller\Admin;

class SettingsController extends AppController
{
    public $actions = [
        'index' => 'Admin.Index',
        'add' => 'Admin.Add',
        'view' => 'Admin.View',
        'edit' => 'Admin.Edit',
        'delete' => 'Admin.Delete',
    ];
}
