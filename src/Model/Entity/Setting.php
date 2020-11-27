<?php
declare(strict_types=1);

namespace Settings\Model\Entity;

use Cake\Core\Configure;
use Cake\ORM\Entity;

/**
 * Setting Entity.
 *
 * @property int $id
 * @property string $scope
 * @property string $plugin
 * @property string $key
 * @property mixed $value
 * @property bool $locked
 */
class Setting extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'id' => true,
        'scope' => true,
        'plugin' => true,
        'key' => true,
        'value' => true,
        'locked' => true,
        '*' => true,
    ];

    /**
     * @var array
     */
    protected $_virtual = [
        'scoped_key',
    ];

    /**
     * @return string
     */
    protected function _getScopedKey(): string
    {
        return sprintf('%s:%s', $this->scope, $this->key);
    }

    /**
     * @return null|mixed
     */
    protected function _getActual()
    {
        return Configure::read($this->key);
    }
}
