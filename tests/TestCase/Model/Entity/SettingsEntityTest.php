<?php
namespace Settings\Test\TestCase\Model\Entity;

use Cake\TestSuite\TestCase;
use Settings\Model\Entity\Setting;

class SettingsEntityTest extends TestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {

    }

    public function testTypes()
    {

    }

    public function testStringValue()
    {
        $value = 'Test';
        $entity = new Setting([
            'name' => 'test_string',
            'type' => Setting::TYPE_STRING,
            'value_string' => $value
        ]);

        $this->assertEquals($value, $entity->value);
        $this->assertInternalType('string', $entity->value);
    }

    public function testBooleanValue()
    {
        $value = true;
        $entity = new Setting([
            'name' => 'test_boolean',
            'type' => Setting::TYPE_BOOLEAN,
            'value_boolean' => $value
        ]);
        $this->assertTrue($entity->value === $value);
        $this->assertInternalType('boolean', $entity->value);

        $value = false;
        $entity = new Setting([
            'name' => 'test_boolean',
            'type' => Setting::TYPE_BOOLEAN,
            'value_boolean' => $value
        ]);
        $this->assertTrue($entity->value === $value);
        $this->assertInternalType('boolean', $entity->value);
    }

    public function testTextValue()
    {
        $value = 'Some Text';
        $entity = new Setting([
            'name' => 'test_text',
            'type' => Setting::TYPE_TEXT,
            'value_text' => $value
        ]);

        $this->assertEquals($value, $entity->value);
        $this->assertInternalType('string', $entity->value);
    }

    public function testIntegerValue()
    {
        $value = 13;
        $entity = new Setting([
            'name' => 'test_integer',
            'type' => Setting::TYPE_INT,
            'value_int' => $value
        ]);

        $this->assertEquals($value, $entity->value);
        $this->assertInternalType('int', $entity->value);
    }

    public function testDoubleValue()
    {
        $value = 13.3333;
        $entity = new Setting([
            'name' => 'test_double',
            'type' => Setting::TYPE_DOUBLE,
            'value_double' => $value
        ]);

        $this->assertEquals($value, $entity->value);
    }

    public function testDoubleValuePrecision()
    {
        $this->markTestSkipped();

        // 4 digit precision
        $value = 13.33339;
        $entity = new Setting([
            'name' => 'test_double_precision',
            'type' => Setting::TYPE_DOUBLE,
            'value_double' => $value
        ]);

        $this->assertEquals(13.3334, $entity->value);
    }

    public function testDateValue()
    {
        $this->markTestIncomplete();
    }

    public function testDatetimeValue()
    {
        $this->markTestIncomplete();
    }

    public function testJsonValue()
    {
        $this->markTestIncomplete();
    }

    public function testXmlValue()
    {
        $this->markTestIncomplete();
    }

    public function testSerializedValue()
    {
        $this->markTestIncomplete();
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