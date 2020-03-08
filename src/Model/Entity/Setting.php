<?php
namespace Settings\Model\Entity;

use Cake\Core\Configure;
use Cake\ORM\Entity;

/**
 * Setting Entity.
 */
class Setting extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'id' => false,
        'scope' => true,
        'key' => true,
        'value' => true,
        '*' => false,
    ];

    /**
     * @var array
     */
    protected $_virtual = [
        'scoped_key',
        'default',
        'actual',
    ];

    /**
     * @return string
     */
    protected function _getScopedKey()
    {
        return sprintf("%s.%s", $this->scope, $this->key);
    }

    protected function _getDefault()
    {
        //@todo Implement me
        return null;
    }

    protected function _getActual()
    {
        return Configure::read($this->key);
    }
}
