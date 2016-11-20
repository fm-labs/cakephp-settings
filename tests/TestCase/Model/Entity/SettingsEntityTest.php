<?php
namespace Settings\Test\TestCase\Model\Entity;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Settings\Model\Entity\Setting;
use Settings\Model\Table\SettingsTable;

class SettingsEntityTest extends TestCase
{

    /**
     * @var SettingsTable
     */
    public $Settings;


    public $fixtures = [
        'plugin.settings.settings'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Settings') ? [] : ['className' => 'Settings\Model\Table\SettingsTable'];
        $this->Settings = TableRegistry::get('Settings', $config);
    }

    public function testTypeGetterSetter()
    {
        // test 1
        $entity = $this->Settings->newEntity();
        $entity->value_type = Setting::TYPE_BOOLEAN;
        $this->assertEquals(Setting::TYPE_BOOLEAN, $entity->value_type);

        // test 2
        $entity = $this->Settings->newEntity();
        $entity->value_type = 'string';
        $this->assertEquals(Setting::TYPE_STRING, $entity->value_type);

        // test 3
        $entity = $this->Settings->newEntity(['value_type' => Setting::TYPE_STRING]);
        $this->assertEquals(Setting::TYPE_STRING, $entity->value_type);

        // test 4
        //$entity = $this->Settings->newEntity(['value_type' => 'string']);
        //$this->assertEquals(Setting::TYPE_STRING, $entity->value_type);
    }

    public function testStringValueGetterSetter()
    {
        // Getter
        $value = 'Test';
        $entity = $this->Settings->newEntity([
            'name' => 'test_string',
            'value_type' => Setting::TYPE_STRING,
            'value_string' => $value
        ]);

        $this->assertEquals($value, $entity->value);
        $this->assertInternalType('string', $entity->value);

        // Setter
        $entity = $this->Settings->newEntity(['value_type' => Setting::TYPE_STRING]);
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
            'value_type' => Setting::TYPE_BOOLEAN,
            'value_boolean' => $value
        ]);
        $this->assertTrue($entity->value === $value);
        $this->assertInternalType('boolean', $entity->value);

        $value = false;
        $entity = $this->Settings->newEntity([
            'name' => 'test_boolean',
            'value_type' => Setting::TYPE_BOOLEAN,
            'value_boolean' => $value
        ]);
        $this->assertTrue($entity->value === $value);
        $this->assertInternalType('boolean', $entity->value);


        // Setter
        $entity = $this->Settings->newEntity(['value_type' => Setting::TYPE_BOOLEAN]);
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
            'value_type' => Setting::TYPE_TEXT,
            'value_text' => $value
        ]);

        $this->assertEquals($value, $entity->value);
        $this->assertInternalType('string', $entity->value);


        // Setter
        $entity = $this->Settings->newEntity(['value_type' => Setting::TYPE_TEXT]);
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
            'value_type' => Setting::TYPE_INT,
            'value_int' => $value
        ]);

        $this->assertEquals($value, $entity->value);
        $this->assertInternalType('int', $entity->value);

        // Setter
        $entity = $this->Settings->newEntity(['value_type' => Setting::TYPE_INT]);
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
            'value_type' => Setting::TYPE_DOUBLE,
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
            'value_type' => Setting::TYPE_DOUBLE,
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