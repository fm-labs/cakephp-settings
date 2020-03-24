<?php $this->assign('title', __d('settings', 'Settings')); ?>
<?php $this->assign('heading', __d('settings', 'Settings')); ?>
<?php /** @var \Settings\Form\SettingsForm $form */ ?>
<div class="settings index">
    <div class="form" style="max-width: 1000px;">
        <?= $this->Form->create($form, ['horizontal' => true]); ?>
        <?= $this->Form->controls($form->getInputs($scope), ['legend' => $scope]); ?>
        <?= $this->Form->submit(__d('settings', 'Update settings')); ?>
        <?= $this->Form->end(); ?>
    </div>
</div>