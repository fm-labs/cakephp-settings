<?php

namespace Settings\Controller\Admin;

class SettingsController extends AppController
{
    public $actions = [
        'index' => 'Backend.Index',
        'add' => 'Backend.Add',
        'view' => 'Backend.View',
        'edit' => 'Backend.Edit',
        'delete' => 'Backend.Delete',
    ];
}
