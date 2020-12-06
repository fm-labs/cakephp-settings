<?php
/**
 * @var \Settings\Form\SettingsForm $form
 */
$scope = $this->get('scope', 'default');
$pluginName = $this->get('pluginName');

$this->extend('base');

$this->Breadcrumbs->add(__d('settings', 'Settings'), ['action' => 'index']);
$this->Breadcrumbs->add($scope, ['action' => 'index', $scope]);

if ($pluginName) {
    $this->Breadcrumbs->add(
        $pluginName,
        ['_name' => 'admin:settings:manage', 'scope' => $scope, 'pluginName' => $pluginName]);
}

//$this->Form->addContextProvider('settings_form', function($request, $context) {
//    if ($context['entity'] instanceof \Settings\Form\SettingsForm) {
//        return new \Settings\View\Form\SettingsFormContext($request, $context);
//    }
//});
$schema = $form->getSettingsManager()->getSchema();
if (empty($schema->getSettings())) {
    echo __('No settings found for {0}', $pluginName);
    return false;
}
?>
<div class="settings index">
    <div class="form">
        <?php
        echo $this->Form->create($form, ['horizontal' => true]);
        foreach ($schema->getGroups() as $groupName => $groupConfig) :
            $_inputs = $form->getInputs($groupName);
            echo $_inputs ? $this->Form->controls($_inputs, ['legend' => $groupConfig['label']]) : '';
        endforeach;
        echo $this->Form->submit(__d('settings', 'Save'));
        echo $this->Form->end();
        ?>
    </div>
</div>
