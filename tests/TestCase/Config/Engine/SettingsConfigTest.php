<?php

namespace Settings\Test\TestCase\Config\Engine;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Settings\Configure\Engine\SettingsConfig;

/**
 * Class SettingsConfigEngine
 *
 * @package Settings\Test\TestCase\Config\Engine
 */
class SettingsConfigTest extends TestCase
{
    /**
     * @var string
     */
    public $path;

    /**
     * @var SettingsConfig
     */
    public $engine;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->path = dirname(dirname(dirname(dirname(__FILE__)))) . DS . 'test_settings' . DS;
        $this->engine = new SettingsConfig($this->path);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        parent::tearDown();
        unset($this->path);
        unset($this->engine);
    }

    /**
     * Test read method
     */
    public function testRead()
    {
        $result = $this->engine->read('test');
        $expected = [
            'test.foo' => 'Bar'
        ];

        $this->assertEquals($expected, $result);
    }

    /**
     * Test dump method
     */
    public function testDump()
    {
        $result = $this->engine->dump('test_dump', [
            'test.foo' => 'Dump'
        ]);
        $this->assertGreaterThan(0, $result);
    }
}
