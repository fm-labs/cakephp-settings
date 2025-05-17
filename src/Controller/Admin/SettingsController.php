<?php
declare(strict_types=1);

namespace Settings\Controller\Admin;

use Exception;
use Settings\Form\SettingsForm;
use Settings\Settings\SettingsManager;

/**
 * Settings Controller
 *
 * @property \Settings\Model\Table\SettingsTable $Settings
 */
class SettingsController extends AppController
{
    /**
     * @var string
     */
    public ?string $modelClass = null;

    /**
     * @var array<string>
     */
    public array $actions = [];

    /**
     * @var \Settings\Settings\SettingsManager
     */
    protected ?SettingsManager $_settingsManager = null;

    /**
     * @return \Settings\Settings\SettingsManager
     */
    public function getSettingsManager()
    {
        if ($this->_settingsManager === null) {
            $manager = new SettingsManager();
            //$this->getEventManager()->dispatch(new Event('Settings.build', null, ['manager' => $manager]));
            $this->_settingsManager = $manager;
        }

        return $this->_settingsManager;
    }

    /**
     * @param string $scope Settings scope
     * @param string|null $pluginName Plugin name
     * @return void
     */
    public function index(string $scope = 'default', string $pluginName = 'App'): void
    {
        $settingsName = $pluginName ? $pluginName . '.settings' : 'settings';
        $settingsManager = $this->getSettingsManager();
        //$settings->autoload();
        $settingsManager->load($settingsName);
        //$settingsManager->apply($settingsManager->getCurrentConfig());

        try {
            $values = $this->Settings->find(type: 'list', keyField: 'key', valueField: 'value')
                ->where(['Settings.plugin' => $pluginName, 'scope' => $scope])
                ->all()
                ->toArray();
            $settingsManager->apply($values);
        } catch (Exception $ex) {
            $this->Flash->error($ex->getMessage());
        }

        $form = new SettingsForm($settingsManager, true);
        if ($this->request->is('post')) {
            if ($form->execute($this->request->getData())) {
                $compiled = $form->getSettingsManager()->getCompiled();
                try {
                    if ($this->Settings->updateValues($scope, $pluginName, $compiled)) {
                        $this->Flash->success(__d('settings', 'Settings updated'));
                        $this->redirect([
                            '_name' => 'admin:settings:manage',
                            'scope' => $scope,
                            'pluginName' => $pluginName]);
                    } else {
                        $this->Flash->error(__d('settings', 'An error occured'));
                    }
                } catch (Exception $ex) {
                    $this->Flash->error($ex->getMessage());
                }
            } else {
                $this->Flash->error(__d('settings', 'Form validation failed'));
            }
        }
        $this->set(compact('pluginName', 'scope', 'settingsManager', 'form'));
        $this->render('form');
    }

    /**
     * @return void
     */
    public function backup(): void
    {
        $this->Flash->warning(__d('settings', 'Not implemented yet'));
        $this->redirect(['action' => 'index']);
    }

    /**
     * @return void
     */
    public function restore(): void
    {
        $this->Flash->warning(__d('settings', 'Not implemented yet'));
        $this->redirect(['action' => 'index']);
    }
}
