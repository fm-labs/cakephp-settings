<?php
namespace Settings\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Settings\Configure\Engine\SettingsConfig;
use Settings\Model\Entity\Setting;

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
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('settings_settings');
        $this->displayField('name');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
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
            ->allowEmpty('ref');
            
        $validator
            ->allowEmpty('scope');
            
        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->requirePresence('value_type', 'create')
            ->notEmpty('value_type');
            
        $validator
            ->add('value_int', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('value_int');
            
        $validator
            ->add('value_double', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('value_double');
            
        $validator
            ->allowEmpty('value_string');
            
        $validator
            ->allowEmpty('value_text');
            
        $validator
            ->add('value_boolean', 'valid', ['rule' => 'boolean'])
            ->allowEmpty('value_boolean');
            
        $validator
            ->add('value_datetime', 'valid', ['rule' => 'datetime'])
            ->allowEmpty('value_datetime');
            
        $validator
            ->allowEmpty('description');
            
        $validator
            ->add('published', 'valid', ['rule' => 'boolean'])
            ->allowEmpty('published');

        return $validator;
    }

    public function afterSave(Event $event, Entity $entity, \ArrayObject $options)
    {
        //SettingsConfig::resetSettingsFilePath(SETTINGS, $entity->ref);
        //return true;
    }

    public function afterDelete(Event $event, Entity $entity, \ArrayObject $options)
    {
        //SettingsConfig::resetSettingsFilePath(SETTINGS, $entity->ref);
        //return true;
    }

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
}
