<?php
namespace Settings\Test\TestCase\Model\Entity;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Settings\Model\Entity\Setting;
use Settings\Model\Table\SettingsTable;

class SettingsEntityTest extends TestCase
{
    public $fixtures = [
        'plugin.settings.settings'
    ];

    public $autoFixtures = true;

    /**
     * @var SettingsTable
     */
    public $Settings;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        $this->Settings = TableRegistry::get('Settings.Settings');
    }

    public function testTypeGetterSetter()
    {
        // test 1
        $entity = $this->Settings->newEntity();
        $entity->type = Setting::TYPE_BOOLEAN;
        $this->assertEquals(Setting::TYPE_BOOLEAN, $entity->type);

        // test 2
        $entity = $this->Settings->newEntity();
        $entity->type = 'string';
        $this->assertEquals(Setting::TYPE_STRING, $entity->type);

        // test 3
        $entity = $this->Settings->newEntity(['type' => Setting::TYPE_STRING]);
        $this->assertEquals(Setting::TYPE_STRING, $entity->type);

        // test 4
        //$entity = $this->Settings->newEntity(['type' => 'string']);
        //$this->assertEquals(Setting::TYPE_STRING, $entity->type);
    }

    public function testStringValueGetterSetter()
    {
        // Getter
        $value = 'Test';
        $entity = $this->Settings->newEntity([
            'name' => 'test_string',
            'type' => Setting::TYPE_STRING,
            'value_string' => $value
        ]);

        $this->assertEquals($value, $entity->value);
        $this->assertInternalType('string', $entity->value);

        // Setter
        $entity = $this->Settings->newEntity(['type' => Setting::TYPE_STRING]);
        $value = 'Hello';

        $entity->value = $value;
        $this->assertEquals($value, $entity->value);
        $this->assertEquals($value, $entity->value_string);
        $this->assertInternalType('string', $entity->value_string);
    }

    public function testBooleanValueGetterSetter()
    {
        // Getter
        $value = true;
        $entity = $this->Settings->newEntity([
            'name' => 'test_boolean',
            'type' => Setting::TYPE_BOOLEAN,
            'value_boolean' => $value
        ]);
        $this->assertTrue($entity->value === $value);
        $this->assertInternalType('boolean', $entity->value);

        $value = false;
        $entity = $this->Settings->newEntity([
            'name' => 'test_boolean',
            'type' => Setting::TYPE_BOOLEAN,
            'value_boolean' => $value
        ]);
        $this->assertTrue($entity->value === $value);
        $this->assertInternalType('boolean', $entity->value);


        // Setter
        $entity = $this->Settings->newEntity(['type' => Setting::TYPE_BOOLEAN]);
        $value = true;

        $entity->value = $value;
        $this->assertEquals($value, $entity->value);
        $this->assertEquals($value, $entity->value_boolean);
        $this->assertInternalType('boolean', $entity->value_boolean);
    }

    public function testTextValueGetterSetter()
    {
        // Getter
        $value = 'Some Text';
        $entity = $this->Settings->newEntity([
            'name' => 'test_text',
            'type' => Setting::TYPE_TEXT,
            'value_text' => $value
        ]);

        $this->assertEquals($value, $entity->value);
        $this->assertInternalType('string', $entity->value);


        // Setter
        $entity = $this->Settings->newEntity(['type' => Setting::TYPE_TEXT]);
        $value = 'Some Text';

        $entity->value = $value;
        $this->assertEquals($value, $entity->value);
        $this->assertEquals($value, $entity->value_text);
        $this->assertInternalType('string', $entity->value_text);
    }

    public function testIntegerValueGetterSetter()
    {
        // Getter
        $value = 13;
        $entity = $this->Settings->newEntity([
            'name' => 'test_integer',
            'type' => Setting::TYPE_INT,
            'value_int' => $value
        ]);

        $this->assertEquals($value, $entity->value);
        $this->assertInternalType('int', $entity->value);

        // Setter
        $entity = $this->Settings->newEntity(['type' => Setting::TYPE_INT]);
        $value = 13;

        $entity->value = $value;
        $this->assertEquals($value, $entity->value);
        $this->assertEquals($value, $entity->value_int);
        $this->assertInternalType('int', $entity->value_int);
    }

    public function testDoubleValueGetterSetter()
    {
        // Getter
        $value = 13.3333;
        $entity = $this->Settings->newEntity([
            'name' => 'test_double',
            'type' => Setting::TYPE_DOUBLE,
            'value_double' => $value
        ]);

        $this->assertEquals($value, $entity->value);
    }

    public function testDoubleValuePrecisionGetterSetter()
    {
        $this->markTestSkipped();

        // 4 digit precision
        $value = 13.33339;
        $entity = $this->Settings->newEntity([
            'name' => 'test_double_precision',
            'type' => Setting::TYPE_DOUBLE,
            'value_double' => $value
        ]);

        $this->assertEquals(13.3334, $entity->value);
    }

    public function testDateValueGetterSetter()
    {
        $this->markTestIncomplete();
    }

    public function testDatetimeValueGetterSetter()
    {
        $this->markTestIncomplete();
    }

    public function testJsonValueGetterSetter()
    {
        $this->markTestIncomplete();
    }

    public function testXmlValueGetterSetter()
    {
        $this->markTestIncomplete();
    }

    public function testSerializedValueGetterSetter()
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