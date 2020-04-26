<?php
declare(strict_types=1);

namespace Settings\Model\Table;

use Cake\Core\Configure;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Settings\SettingsManager;

/**
 * Settings Model
 *
 */
class SettingsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('settings');
        $this->setDisplayField('key');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }

    /**
     * @param $list
     * @param $scope
     * @return array
     */
    public function updateSettings($list, $scope)
    {
        $entities = [];
        foreach ($list as $key => $val) {
            $entities[$key] = $this->updateSetting($key, $val, $scope);
        }

        return $entities;
    }

    /**
     * @param $key
     * @param $value
     * @param $scope
     * @return bool|\Cake\Datasource\EntityInterface|\Cake\ORM\Entity|mixed
     */
    public function updateSetting($key, $value, $scope)
    {
        $setting = $this->find()->where(['key' => $key, 'scope' => $scope])->first();
        if (!$setting) {
            $setting = $this->newEmptyEntity();
        }

        $setting = $this->patchEntity($setting, compact('key', 'value', 'scope'));
        if ($setting->getErrors()) {
            debug($setting->getErrors());

            return $setting;
        }

        return $this->save($setting);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): \Cake\Validation\Validator
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmptyString('id', 'create');

        $validator
            ->allowEmptyString('scope');

        $validator
            ->requirePresence('key')
            ->notEmptyString('key');

        $validator
            ->allowEmptyString('value');

        return $validator;
    }

    /**
     * @param \Cake\Event\Event $event
     * @param \Cake\ORM\Entity $entity
     * @param \ArrayObject $options
     */
    public function afterSave(\Cake\Event\EventInterface $event, Entity $entity, \ArrayObject $options)
    {
        //$this->dumpSettingsConfig($entity->scope);
    }

    /**
     * @param \Cake\Event\Event $event
     * @param \Cake\ORM\Entity $entity
     * @param \ArrayObject $options
     */
    public function afterDelete(\Cake\Event\EventInterface $event, Entity $entity, \ArrayObject $options)
    {
        //$this->dumpSettingsConfig($entity->scope);
    }

    /**
     * @return array
     */
    public function listByKeys()
    {
        //@TODO Refactor with Collection methods
        $list = [];
        $data = $this->find()->all()->toArray();
        array_walk($data, function ($entity) use (&$list) {
            $list[$entity->key] = $entity->id;
        });

        return $list;
    }

    /**
     * @param string $scope
     * @return array
     */
    public function getCompiled($scope = 'default')
    {
        return (new SettingsManager($scope))->getCompiled();
    }

    /**
     * @param $scope
     */
    public function dumpSettingsConfig($scope)
    {
        Configure::dump($scope, 'settings', ['Settings']);
    }
}
