<?php $this->Breadcrumbs->add(__d('settings','Settings'), ['action' => 'index']); ?>
<?php $this->Breadcrumbs->add(__d('settings','Edit {0}', __d('settings','Setting'))); ?>
<?php $this->assign('title', __d('settings','Settings')); ?>
<?php $this->assign('heading', __d('settings','Edit {0}', __d('settings','Setting'))); ?>
<div class="settings form">
    <?= $this->Form->create($setting, ['class' => 'setting']); ?>
    <?php
    echo $this->Form->input('scope');
    echo $this->Form->input('key');
    echo $this->Form->input('value');
    ?>
    <?= $this->Form->button(__d('settings','Submit')) ?>
    <?= $this->Form->end() ?>

</div>