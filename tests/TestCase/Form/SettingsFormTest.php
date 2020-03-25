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
        //$this->form = new SettingsForm();
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        //unset($this->form);
    }

    /**
     * Test manager method
     */
    public function testManagerGetter()
    {
        $this->markTestIncomplete();
        $this->assertInstanceOf('Settings\SettingsManager', $this->form->manager());
    }

    /**
     * Test manager method
     */
    public function testManagerSetter()
    {
        $this->markTestIncomplete();
        $this->form->manager(new TestSettingsManager());
        $this->assertInstanceOf('Settings\Test\TestCase\TestSettingsManager', $this->form->manager());
    }

    /**
     * Test schema method
     */
    public function testSchemaGetter()
    {
        $this->markTestIncomplete();
        $this->assertInstanceOf('Cake\Form\Schema', $this->form->getSchema());
    }

    /**
     * Test schema method
     */
    public function testSchemaSetter()
    {
        $this->markTestIncomplete();
        $schema = new Schema();
        $schema->addField('custom_schema_field', []);
        $this->form->schema($schema);

        $result = $this->form->getSchema();
        $this->assertNotNull($result->field('custom_schema_field'));
    }

    /**
     * Test inputs method
     */
    public function testInputsGetter()
    {
        $this->markTestIncomplete();
        $result = $this->form->getInputs();
        $this->assertInternalType('array', $result);

        $this->markTestIncomplete('Data not tested');
    }

    /**
     * Test inputs method
     */
    public function testInputsSetter()
    {
        $this->markTestIncomplete();
        //$this->form->setInputs(['test_input' => ['type' => 'text']]);
        $result = $this->form->getInputs();

        $this->assertArrayHasKey('test_input', $result);
    }

    /**
     * Test value method
     */
    public function testValue()
    {
        $this->markTestIncomplete();
        $this->form->setSettingsManager(new TestSettingsManager());
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
