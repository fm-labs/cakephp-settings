<?php
namespace Settings\Model\Entity;

use Cake\Core\Exception\Exception;
use Cake\ORM\Entity;
use Banana\Model\Table\SettingsTable;

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
        '*' => false
    ];

    protected $_virtual = [
        'scoped_key'
    ];

    protected function _getScopedKey()
    {
        return sprintf("%s.%s", $this->scope, $this->key);
    }
}
