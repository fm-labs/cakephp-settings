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
<div class="index container-fluid">
    <div class="row">
        <div class="col col-sm-3 col-md-3 col-lg-2">
            <nav class="nav flex-column">
                <div class="nav-item"><?= $this->Html->link(
                        __('Application'),
                        ['_name' => 'admin:settings:manage', 'scope' => $scope, 'pluginName' => 'App'],
                        ['class' => 'nav-link py-1']
                    ); ?></div>
                <?php foreach (\Cake\Core\Plugin::loaded() as $_pluginName) : ?>
                    <?php
                    $options = [];
                    $linkClass = 'nav-link py-1';

                    $active = $plugin == $_pluginName;
                    if ($active) {
                        $linkClass .= ' active fw-bold';
                        $options['aria-current'] = 'page';
                    }
                    $options['class'] = $linkClass;
                    ?>
                    <li class="nav-item"><?= $this->Html->link(
                            $_pluginName,
                            ['_name' => 'admin:settings:manage', 'scope' => $scope, 'pluginName' => $_pluginName],
                            $options
                        ); ?></li>
                <?php endforeach; ?>
            </nav>
        </div>
        <div class="col col-xs-12 col-sm-9 col-md-9 col-lg-10">
            <?= $this->fetch('content'); ?>
        </div>
    </div>
</div>
