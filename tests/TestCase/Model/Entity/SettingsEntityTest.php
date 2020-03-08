<?php
namespace Settings\Test\TestCase\Model\Entity;

use Cake\TestSuite\TestCase;
use Settings\Model\Entity\Setting;

class SettingsEntityTest extends TestCase
{

    /**
     * @var Setting
     */
    public $Setting;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Setting = new Setting();
    }

    public function testGetScopedKey()
    {
        $this->Setting = new Setting([
            'scope' => 'test',
            'key' => 'foo',
            'value' => 'bar',
        ]);

        $this->assertEquals('test.foo', $this->Setting->scoped_key);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
    }
}
