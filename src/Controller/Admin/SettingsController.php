<?php

namespace Settings\Controller\Admin;

use Backend\Controller\BackendActionsTrait;

class SettingsController extends AppController
{
    use BackendActionsTrait;

    public $modelClass = "Settings.Settings";

    public $actions = [
        'index' => 'Backend.Index',
        'add' => 'Backend.Add',
        'view' => 'Backend.View',
        'edit' => 'Backend.Edit',
        'delete' => 'Backend.Delete',
    ];
}
