<?php
namespace Settings\Model\Entity;

use Cake\Core\Exception\Exception;
use Cake\ORM\Entity;

/**
 * Setting Entity.
 */
class Setting extends Entity
{
    const KEY_PREFIX = 'Settings';

    const TYPE_STRING = 1;

    const TYPE_INT = 2;

    const TYPE_DOUBLE = 4;

    const TYPE_BOOLEAN = 8;

    const TYPE_TEXT = 16;

    const TYPE_DATE = 32;

    const TYPE_DATETIME = 64;

    const TYPE_JSON = 128;

    const TYPE_XML = 256;

    const TYPE_SERIALIZED = 512;

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'ref' => true,
        'scope' => true,
        'name' => true,
        'type' => true,
        'value_int' => true,
        'value_double' => true,
        'value_string' => true,
        'value_text' => true,
        'value_boolean' => true,
        'value_datetime' => true,
        'description' => true,
        'published' => true,
        'key' => true, // virtual
    ];

    protected function _setKey($key)
    {
        $value = $key;
        $dotPrefix = (self::KEY_PREFIX) ? self::KEY_PREFIX . '.' : '';
        if (preg_match('/^'. preg_quote($dotPrefix, '/') . '/', $value)) {
            $value = substr($value, strlen($dotPrefix));
        }
        list($scope, $name) = pluginSplit($value);
        $this->scope = $scope;
        $this->name = $name;
        return $key;
    }

    protected function _getKey()
    {
        return join('.', array_filter([self::KEY_PREFIX, $this->scope, $this->name]));
    }

    protected function _setValue($value)
    {
        //not supported yet
    }

    protected function _getValue()
    {
        switch ((int)$this->type)
        {
            case self::TYPE_BOOLEAN:
                return (bool)$this->value_boolean;
            case self::TYPE_INT:
                return (int)$this->value_int;
            case self::TYPE_DOUBLE:
                return (double)$this->value_double;
            case self::TYPE_STRING:
                return (string)$this->value_string;
            case self::TYPE_TEXT:
                return (string)$this->value_text;
            default:
                throw new Exception(sprintf("Unknown setting type '%s'", $this->type));
        }
    }

    public static function typeMap()
    {
        return [
            'int' => self::TYPE_INT,
            'double' => self::TYPE_DOUBLE,
            'string' => self::TYPE_STRING,
            'text' => self::TYPE_TEXT,
            'date' => self::TYPE_DATE,
            'datetime' => self::TYPE_DATETIME,
            'boolean' => self::TYPE_BOOLEAN
        ];
    }

    public static function mapType($typeStr) {
        if (isset(self::typeMap()[$typeStr])) {
            return self::typeMap()[$typeStr];
        }
        return null;
    }
}
