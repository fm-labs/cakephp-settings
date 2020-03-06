<?php $this->assign('title', __d('settings', 'Settings')); ?>
<?php $this->assign('heading', __d('settings', 'Settings')); ?>
<?php
$settings = new \Cake\Collection\Collection($manager->getSettings());
debug($settings->toArray());
$grouped = $settings->groupBy('group');
debug($grouped->toArray());
?>
<div class="settings index">


    <div class="form" style="max-width: 1000px;">

        <?= $this->Form->create(null, ['horizontal' => true]); ?>

            <?php foreach ($result as $key => $setting) : ?>
                <?= $this->Form->input($key, $setting['input']); ?>
            <?php endforeach; ?>

        <?= $this->Form->submit(__d('settings', 'Update settings')); ?>
        <?= $this->Form->end(); ?>

        <!--
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <?= h($key); ?>
                    </div>
                    <div class="box-body">
                    </div>
                </div>
                -->
    </div>

    <?php debug($manager->getSettings()); ?>
    <?php debug($result); ?>
    <?php debug($manager->getCompiled()); ?>
</div>