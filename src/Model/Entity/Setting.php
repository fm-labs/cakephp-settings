<?php
namespace Settings\Model\Entity;

use Cake\Core\Exception\Exception;
use Cake\ORM\Entity;

/**
 * Setting Entity.
 */
class Setting extends Entity
{
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

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'ref' => true,
        'scope' => true,
        'name' => true,
        'value_type' => true,
        'value' => true,
        'value_int' => true,
        'value_double' => true,
        'value_string' => true,
        'value_text' => true,
        'value_boolean' => true,
        'value_datetime' => true,
        'description' => true,
        'published' => true,
        'default' => true,
    ];

    /**
     * Virtual field 'value' setter
     * Set value and assign to property based on type
     *
     * @param $value
     * @return mixed
     */
    protected function _setValue($value)
    {
        switch ($this->value_type)
        {
            case static::TYPE_BOOLEAN:
                $this->_properties['value_boolean'] = (bool) $value;
                break;
            case static::TYPE_INT:
                $this->_properties['value_int'] = (int) $value;
                break;
            case static::TYPE_DOUBLE:
                $this->_properties['value_double'] = (double) $value;
                break;
            case static::TYPE_STRING:
                $this->_properties['value_string'] = (string) $value;
                break;
            case static::TYPE_TEXT:
                $this->_properties['value_text'] = (string) $value;
                break;
            case static::TYPE_OTHER:
            default:
                // @TODO Remove exception, set value to NULL and add value validator
                throw new Exception(sprintf("Unknown setting value_type '%s'", $this->value_type));
        }

        return $value;
    }

    /**
     * Virtual field 'value' getter
     * Return value based on value_type
     *
     * @return bool|float|int|string
     */
    protected function _getValue()
    {
        if (!array_key_exists('value', $this->_properties)) {

            $val = null;
            switch ($this->value_type)
            {
                case static::TYPE_BOOLEAN:
                    $val = (bool) $this->value_boolean;
                    break;
                case static::TYPE_INT:
                    $val = (int) $this->value_int;
                    break;
                case static::TYPE_DOUBLE:
                    $val = (double) $this->value_double;
                    break;
                case static::TYPE_STRING:
                    $val = (string) $this->value_string;
                    break;
                case static::TYPE_TEXT:
                    $val = (string) $this->value_text;
                    break;
                case static::TYPE_OTHER:
                default:
            }
            $this->_properties['value'] = $val;
        }


        return $this->_properties['value'];
    }

    /**
     * Returns the value_type map
     * @return array
     */
    public static function typeMap()
    {
        return [
            'int' => static::TYPE_INT,
            'double' => static::TYPE_DOUBLE,
            'string' => static::TYPE_STRING,
            'text' => static::TYPE_TEXT,
            'date' => static::TYPE_DATE,
            'datetime' => static::TYPE_DATETIME,
            'boolean' => static::TYPE_BOOLEAN
        ];
    }

    /**
     * Map type-string to integer representation
     *
     * @param $typeStr
     * @return int
     */
    public static function mapTypeStr($typeStr) {
        $map = static::typeMap();

        if (isset($map[$typeStr])) {
            return $map[$typeStr];
        }
        return static::TYPE_OTHER;
    }

    /**
     * Map type to string representation
     *
     * @param $typeInt
     * @return int|null
     */
    public static function mapType($typeInt) {
        $map = array_flip(static::typeMap());

        if (isset($map[$typeInt])) {
            return $map[$typeInt];
        }
        return null;
    }
}
