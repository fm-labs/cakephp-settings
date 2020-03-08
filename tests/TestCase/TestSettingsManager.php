<?php

namespace Settings\Test\TestCase;

use Settings\SettingsManager;

/**
 * Class TestSettingsManager
 *
 * @package Settings\Test\TestCase
 */
class TestSettingsManager extends SettingsManager
{
    /**
     * @param string $scope
     * @param array $settings
     */
    public function __construct($scope = 'test', $settings = [])
    {
        //parent::__construct($scope, $settings);
    }

    /**
     * Load dummy settings
     */
    public function _loadSettings()
    {
        $this->_settings = [
            'test' => [
                'test_string' => [
                    'type' => 'string',
                    'inputType' => 'text',
                ],
                'test_int' => [
                    'type' => 'int',
                ],
                'test_bool' => [
                    'type' => 'boolean',
                ],
            ],
        ];
    }

    /**
     * Load dummy values
     */
    public function _loadValues()
    {
        $this->_values = [
            'test_string' => 'Some Text',
            'test_int' => 3,
            'test_bool' => true,
        ];
    }
}
