<?php

namespace Settings\Test\TestCase\Form;

use Cake\Form\Schema;
use Cake\TestSuite\TestCase;
use Settings\Form\SettingsForm;
use Settings\Test\TestCase\TestSettingsManager;

/**
 * Class SettingsFormTest
 *
 * @package Settings\Test\TestCase\Form
 */
class SettingsFormTest extends TestCase
{
    /**
     * @var SettingsForm
     */
    public $form;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->form = new SettingsForm();
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->form);
    }

    /**
     * Test manager method
     */
    public function testManagerGetter()
    {
        $this->assertInstanceOf('Settings\SettingsManager', $this->form->manager());
    }

    /**
     * Test manager method
     */
    public function testManagerSetter()
    {
        $this->form->manager(new TestSettingsManager());
        $this->assertInstanceOf('Settings\Test\TestCase\TestSettingsManager', $this->form->manager());
    }

    /**
     * Test schema method
     */
    public function testSchemaGetter()
    {
        $this->assertInstanceOf('Cake\Form\Schema', $this->form->schema());
    }

    /**
     * Test schema method
     */
    public function testSchemaSetter()
    {
        $schema = new Schema();
        $schema->addField('custom_schema_field', []);
        $this->form->schema($schema);

        $result = $this->form->schema();
        $this->assertNotNull($result->field('custom_schema_field'));
    }

    /**
     * Test inputs method
     */
    public function testInputsGetter()
    {
        $result = $this->form->inputs();
        $this->assertInternalType('array', $result);

        $this->markTestIncomplete('Data not tested');
    }

    /**
     * Test inputs method
     */
    public function testInputsSetter()
    {
        $this->form->inputs(['test_input' => ['type' => 'text']]);
        $result = $this->form->inputs();

        $this->assertArrayHasKey('test_input', $result);
    }

    /**
     * Test value method
     */
    public function testValue()
    {
        $this->form->manager(new TestSettingsManager());
        $this->assertEquals($this->form->value('test_string'), 'Some Text');
        $this->assertEquals($this->form->value('test_int'), 3);
        $this->assertEquals($this->form->value('test_bool'), true);
    }

    /**
     * Test input method
     */
    public function testExecute()
    {
        $this->markTestIncomplete();
    }
}