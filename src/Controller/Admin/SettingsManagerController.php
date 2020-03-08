<?php

namespace Settings\Controller\Admin;

use Banana\Banana;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\Utility\Hash;
use Settings\Form\SettingsForm;
use Settings\Model\Table\SettingsTable;
use Settings\SettingsManager;

/**
 * Settings Controller
 *
 * @property SettingsTable $Settings
 */
class SettingsManagerController extends AppController
{
    /**
     * @var string
     */
    public $modelClass = false;

    public $actions = [
        'edit' => 'Backend.Edit',
        'view' => 'Backend.View',
    ];

    /**
     * @var SettingsManager
     */
    public $_settingsManager;

    /**
     * @return SettingsManager
     */
    public function settingsManager()
    {
        if (!$this->_settingsManager) {
            $manager = new SettingsManager();
            $this->getEventManager()->dispatch(new Event('Settings.build', null, ['manager' => $manager]));
            $this->_settingsManager = $manager;
        }

        return $this->_settingsManager;
    }

    /**
     * Load settings values from persistent storage
     *
     * @param string $scope Settings scope
     * @return array
     */
    protected function _loadValues($scope)
    {
        $values = [];
        $settings = $this->Settings
            ->find()
            ->where(['Settings.scope' => $scope])
            ->all();

        foreach ($settings as $setting) {
            $values[$setting->key] = $setting->value;
        }

        return $values;
    }

    protected function _saveValues($scope, $compiled)
    {
        $settings = $this->Settings
            ->find()
            ->where(['Settings.scope' => $scope])
            ->all();

        $copy = $compiled;

        foreach ($settings as $setting) {
            $key = $setting->key;
            if (isset($compiled[$key])) {
                $setting->set('value', $compiled[$key]);
                unset($compiled[$key]);
            } else {
                $setting->set('value', null);
            }

            if (!$this->Settings->save($setting)) {
                Log::error("Failed saving setting for key $key", ['settings']);

                return false;
            }
        }

        foreach ($compiled as $key => $val) {
            $setting = $this->Settings->newEntity(['key' => $key, 'value' => $val, 'scope' => $scope]);
            if (!$this->Settings->save($setting)) {
                Log::error("Failed adding setting for key $key", ['settings']);

                return false;
            }
        }

        Cache::clear(false, 'settings');
        Configure::write($copy);

        return true;
    }

    public function manage($scope = null, $group = null)
    {
        $scope = ($scope) ?: SETTINGS_SCOPE;
        $values = $this->_loadValues($scope);
        $this->settingsManager()->apply($values);

        if ($this->request->is('post')) {
            $values = Hash::flatten($this->request->data());
            $this->settingsManager()->apply($values);
            $compiled = $this->_settingsManager->getCompiled();
            if (!$this->_saveValues($scope, $compiled)) {
                $this->Flash->error("Failed to update values");
            } else {
                $this->Flash->success("Saved!");
                $this->redirect(['action' => 'manage', $scope]);
            }
        }

        $this->set('scope', $scope);
        $this->set('group', $group);
        $this->set('manager', $this->settingsManager());
        $this->set('result', $this->settingsManager()->describe());
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('manager', $this->settingsManager());
        $this->set('result', $this->settingsManager()->describe());
    }

    /**
     * Form method
     *
     * @param string $scope Settings scope
     * @return void
     */
    public function form($scope = SETTINGS_SCOPE)
    {
        $settingsForm = new SettingsForm($this->settingsManager());

        if ($this->request->is(['put', 'post'])) {
            // apply
            $settingsForm->execute($this->request->data);

            // compile
            $compiled = $settingsForm->manager()->getCompiled();
            //Configure::write($compiled);

            // update
            if ($this->Settings->updateSettings($compiled, $scope)) {
                // dump
                $settingsForm->manager()->dump();

                $this->Flash->success('Settings updated');
                $this->redirect(['action' => 'index', $scope]);
            }
        }

        //$this->set('settings', $settings);
        $this->set('scope', $scope);
        $this->set('form', $settingsForm);
        $this->set('_serialize', ['settings']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $setting = $this->Settings->newEntity();
        if ($this->request->is('post')) {
            $setting = $this->Settings->patchEntity($setting, $this->request->data);
            if ($this->Settings->save($setting)) {
                $this->Flash->success(__d('settings', 'The {0} has been saved.', __d('settings', 'setting')));

                return $this->redirect(['action' => 'edit', $setting->id]);
            } else {
                $this->Flash->error(__d('settings', 'The {0} could not be saved. Please, try again.', __d('settings', 'setting')));
            }
        }
        $this->set(compact('setting'));
        $this->set('valueTypes', $this->Settings->listValueTypes());
        $this->set('_serialize', ['setting']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Setting id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $setting = $this->Settings->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $setting = $this->Settings->patchEntity($setting, $this->request->data);
            if ($this->Settings->save($setting)) {
                //$this->Settings->dump();
                $this->Flash->success(__d('settings', 'The {0} has been saved.', __d('settings', 'setting')));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('settings', 'The {0} could not be saved. Please, try again.', __d('settings', 'setting')));
            }
        }
        $this->set(compact('setting'));
        $this->set('_serialize', ['setting']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Setting id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $setting = $this->Settings->get($id);
        if ($this->Settings->delete($setting)) {
            $this->Flash->success(__d('settings', 'The {0} has been deleted.', __d('settings', 'setting')));
        } else {
            $this->Flash->error(__d('settings', 'The {0} could not be deleted. Please, try again.', __d('settings', 'setting')));
        }

        return $this->redirect(['action' => 'index']);
    }
}
