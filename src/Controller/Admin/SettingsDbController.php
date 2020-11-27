<?php
declare(strict_types=1);

namespace Settings\Controller\Admin;

use Cupcake\Cupcake;
use Settings\Form\SettingsForm;
use Settings\Settings\SettingsManager;

class SettingsDbController extends AppController
{
    public $actions = [
        'index' => 'Admin.Index',
        'add' => 'Admin.Add',
        'view' => 'Admin.View',
        'edit' => 'Admin.Edit',
        'delete' => 'Admin.Delete',
    ];


    /**
     * @return void
     */
    public function index(): void
    {
    }

    /**
     * @return void
     */
    public function edit(): void
    {
    }

    /**
     * @return void
     */
    public function view(): void
    {
    }

    /**
     * @return void
     */
    public function add(): void
    {
    }

    /**
     * @return void
     */
    public function delete(): void
    {
    }
}
