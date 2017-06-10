<?php
namespace Settings\Model\Table;

use Cake\Core\Configure;
use Cake\Event\Event;
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

    /*
    const TYPE_STRING = 'string';
    const TYPE_INT = 'int';
    const TYPE_DOUBLE = 'double';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_TEXT = 'text';
    const TYPE_DATE = 'date';
    const TYPE_DATETIME = 'datetime';
    const TYPE_JSON = 'json';
    const TYPE_XML = 'xml';
    const TYPE_SERIALIZED = 'serialized';
    const TYPE_OTHER = 'other' ; // @deprecated
    */

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('settings');
        $this->displayField('key');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
    }

    /**
     * @param $list
     * @param $scope
     * @return array
     */
    public function updateSettings($list, $scope)
    {
        debug("updating");
        debug($list);
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
     * @return bool|\Cake\Datasource\EntityInterface|Entity|mixed
     */
    public function updateSetting($key, $value, $scope)
    {
        $setting = $this->find()->where(['key' => $key, 'scope' => $scope])->first();
        if (!$setting) {
            $setting = $this->newEntity();
        }
        $setting = $this->patchEntity($setting, compact('key', 'value', 'scope'));
        if ($setting->errors()) {
            debug($setting->errors());

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
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('scope');

        $validator
            ->requirePresence('key')
            ->notEmpty('key');

        $validator
            ->allowEmpty('value');

        return $validator;
    }

    /**
     * @param Event $event
     * @param Entity $entity
     * @param \ArrayObject $options
     */
    public function afterSave(Event $event, Entity $entity, \ArrayObject $options)
    {
        //$this->dumpSettingsConfig($entity->scope);
    }

    /**
     * @param Event $event
     * @param Entity $entity
     * @param \ArrayObject $options
     */
    public function afterDelete(Event $event, Entity $entity, \ArrayObject $options)
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
        return (new SettingsManager([], $scope))->getCompiled();
    }

    /**
     * @param $scope
     */
    public function dumpSettingsConfig($scope)
    {
        Configure::dump($scope, 'settings', ['Settings']);
    }
}
