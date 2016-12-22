<?php
namespace Settings\Model\Entity;

use Cake\Core\Exception\Exception;
use Cake\ORM\Entity;
use Settings\Model\Table\SettingsTable;

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
        'scope' => true,
        'title' => true,
        'desc' => true,
        'key' => true,
        'value_type' => true,
        'value' => true,
        'default_value' => true,
        'is_required' => true,
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
            case SettingsTable::TYPE_BOOLEAN:
                $value = (bool) $value;
                break;
            case SettingsTable::TYPE_INT:
                $value = (int) $value;
                break;
            case SettingsTable::TYPE_DOUBLE:
                $value = (double) $value;
                break;
            case SettingsTable::TYPE_STRING:
                $value = (string) $value;
                $value = (string) $value;
                break;
            case SettingsTable::TYPE_OTHER:
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

        $value = (isset($this->_properties['value'])) ? $this->_properties['value'] : null;


        switch ($this->value_type)
        {
            case SettingsTable::TYPE_BOOLEAN:
                $value = (bool) $value;
                break;
            case SettingsTable::TYPE_INT:
                $value = (int) $value;
                break;
            case SettingsTable::TYPE_DOUBLE:
                $value = (double) $value;
                break;
            case SettingsTable::TYPE_STRING:
                $value = (string) $value;
                break;
            case SettingsTable::TYPE_TEXT:
                $value = (string) $value;
                break;
            case SettingsTable::TYPE_OTHER:
            default:
                break;
        }

        return $value;
    }

    /**
     * Returns the value_type map
     * @return array
     * @deprecated
     */
    public static function typeMap()
    {
        return [
            'int' => SettingsTable::TYPE_INT,
            'double' => SettingsTable::TYPE_DOUBLE,
            'string' => SettingsTable::TYPE_STRING,
            'text' => SettingsTable::TYPE_TEXT,
            'date' => SettingsTable::TYPE_DATE,
            'datetime' => SettingsTable::TYPE_DATETIME,
            'boolean' => SettingsTable::TYPE_BOOLEAN
        ];
    }

    /**
     * Map type-string to integer representation
     *
     * @param $typeStr
     * @return int
     * @deprecated
     */
    public static function mapTypeStr($typeStr) {
        $map = SettingsTable::typeMap();

        if (isset($map[$typeStr])) {
            return $map[$typeStr];
        }
        return SettingsTable::TYPE_OTHER;
    }

    /**
     * Map type to string representation
     *
     * @param $typeInt
     * @return int|null
     * @deprecated
     */
    public static function mapType($typeInt) {
        $map = array_flip(SettingsTable::typeMap());

        if (isset($map[$typeInt])) {
            return $map[$typeInt];
        }
        return null;
    }
}
