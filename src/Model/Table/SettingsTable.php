<?php
declare(strict_types=1);

namespace Settings\Model\Table;

use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\Utility\Inflector;
use Cake\Validation\Validator;
use Settings\Configure\Engine\SettingsConfig;

/**
 * Settings Model
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
     * Alias for updateValue().
     *
     * @param string $scope Setting scope
     * @param string $plugin Plugin name
     * @param string $key Setting key
     * @param string $value Setting value
     * @return bool|\Cake\Datasource\EntityInterface|\Cake\ORM\Entity|mixed
     */
    public function addValue(string $scope, string $plugin, string $key, $value)
    {
        return $this->updateValue($scope, $plugin, $key, $value);
    }

    /**
     * Upsert setting value.
     *
     * @param string $scope Setting scope
     * @param string $plugin Plugin name
     * @param string $key Setting key
     * @param string $value Setting value
     * @return bool|\Cake\Datasource\EntityInterface|\Cake\ORM\Entity|mixed
     */
    public function updateValue(string $scope, string $plugin, string $key, $value)
    {
        $search = ['key' => $key, 'scope' => $scope, 'plugin' => $plugin];
        $setting = $this->findOrCreate($search);

        $setting = $this->patchEntity($setting, compact('value'));
        if ($setting->getErrors()) {
            debug($setting->getErrors());
            //@TODO throw exception
            return $setting;
        }

        return $this->save($setting);
    }

    /**
     * Upsert multiple settings values using a transaction.
     *
     * @param string $scope Setting scope
     * @param string $plugin Plugin name
     * @param array $values Key-value pairs of settings values
     * @return bool
     * @throws \Exception
     */
    public function updateValues(string $scope, string $plugin, array $values): bool
    {
        $settingIds = $this->find('list', ['keyField' => 'key', 'valueField' => 'id'])
            ->where(['scope' => $scope, 'plugin' => $plugin])
            ->all()
            ->toArray();

        $entities = [];
        foreach ($values as $key => $value) {
            $data = [];
            $data['scope'] = $scope;
            $data['plugin'] = $plugin;
            $data['key'] = $key;
            $data['value'] = $value;
            $data['id'] = $settingIds[$key] ?? null;

            /** @var \Settings\Model\Entity\Setting $_setting */
            $_setting = $this->newEntity($data);
            $_setting->id = $settingIds[$key] ?? null;

            if ($_setting->getErrors()) {
                debug($_setting->getErrors());
                Log::error("Setting with key $key has errors", ['settings']);
                //return false;
            }

            $entities[] = $_setting;
        }

        if (!$this->saveMany($entities)) {
            return false;
        }

        # return Configure::dump(Inflector::underscore($plugin), 'settings', array_keys($values));
        # $settingsConfig = new SettingsConfig();
        # return $settingsConfig->dump(Inflector::underscore($plugin), $values);
        return true;
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
            ->requirePresence('plugin')
            ->notEmptyString('plugin');

        $validator
            ->requirePresence('scope')
            ->notEmptyString('scope');

        $validator
            ->requirePresence('key')
            ->notEmptyString('key');

        $validator
            ->allowEmptyString('value');

        $validator
            ->allowEmptyString('locked')
            ->boolean('locked');

        return $validator;
    }

    /**
     * @param \Cake\Event\EventInterface $event Event
     * @param \Cake\ORM\Entity $entity Entity
     * @param \ArrayObject $options Options
     * @return void
     */
    public function afterSave(\Cake\Event\EventInterface $event, Entity $entity, \ArrayObject $options)
    {
        $this->clearSettingsCache($entity);
    }

    /**
     * @param \Cake\Event\EventInterface $event Event
     * @param \Cake\ORM\Entity $entity Entity
     * @param \ArrayObject $options Options
     * @return void
     */
    public function afterSaveCommit(\Cake\Event\EventInterface $event, Entity $entity, \ArrayObject $options)
    {
        $this->clearSettingsCache($entity);
    }

    /**
     * @param \Cake\Event\EventInterface $event Event
     * @param \Cake\ORM\Entity $entity Entity
     * @param \ArrayObject $options Options
     * @return void
     */
    public function afterDelete(\Cake\Event\EventInterface $event, Entity $entity, \ArrayObject $options)
    {
        $this->clearSettingsCache($entity);
    }

    /**
     * @param \Cake\ORM\Entity $entity Settings entity
     * @return bool
     */
    public function clearSettingsCache(Entity $entity): bool
    {
        return SettingsConfig::clearCache($entity->plugin);
    }
}
