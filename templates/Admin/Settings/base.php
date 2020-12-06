<?php
/**
 * @var \Settings\Form\SettingsForm $form
 */
$scope = $this->get('scope');
$plugin = $this->get('pluginName');


$this->Toolbar->addLink(
    __d('settings', 'Backup'),
    ['controller' => 'Settings', 'action' => 'backup'],
    ['data-icon' => 'download']
);
$this->Toolbar->addLink(
    __d('settings', 'Restore'),
    ['controller' => 'Settings', 'action' => 'restore'],
    ['data-icon' => 'upload']
);

?>
<div class="index">
    <div class="grid">
        <div class="col col-md-3 col-sm-3 hidden-xs">
            <h3><?= __('Settings'); ?></h3>
            <ul class="nav nav-pills nav-stacked">
                <li><?= $this->Html->link(
                    __('Application'),
                    ['_name' => 'admin:settings:manage', 'scope' => $scope, 'pluginName' => 'App']
                ); ?></li>
                <?php foreach (\Cake\Core\Plugin::loaded() as $_pluginName) : ?>
                    <li class="<?= $plugin == $_pluginName ? 'active' : ''; ?>"><?= $this->Html->link(
                        $_pluginName,
                        ['_name' => 'admin:settings:manage', 'scope' => $scope, 'pluginName' => $_pluginName],
                        ['class' => '']
                    ); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="col col-md-9 col-sm-9 col-xs-12">
            <?= $this->fetch('content'); ?>
        </div>
    </div>
</div>
