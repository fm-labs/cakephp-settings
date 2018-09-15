<?php

namespace Settings\Test\TestCase\View\Form;

use Cake\TestSuite\TestCase;
use Settings\Form\SettingsForm;
use Settings\Test\TestCase\TestSettingsManager;
use Settings\View\Form\SettingsFormContext;

/**
 * Class SettingsFormContextTest
 *
 * @package Settings\Test\TestCase\View\Form
 */
class SettingsFormContextTest extends TestCase
{

    /**
     * @var SettingsFormContext
     */
    public $context;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $request = $this->getMockBuilder('Cake\Network\Request')->getMock();
        $form = new SettingsForm(new TestSettingsManager());

        $this->context = new SettingsFormContext($request, ['entity' => $form]);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Test primaryKey method
     */
    public function testPrimaryKey()
    {
        $this->markTestIncomplete();
        $this->assertEquals([], $this->context->primaryKey());
    }

    /**
     * Test isPrimaryKey method
     */
    public function testIsPrimaryKey()
    {
        $this->markTestIncomplete();
        $this->assertFalse($this->context->isPrimaryKey('id'));
        $this->assertFalse($this->context->isPrimaryKey('foo'));
        $this->assertFalse($this->context->isPrimaryKey('baz'));
    }

    /**
     * Test isCreate method
     */
    public function testIsCreate()
    {
        $this->markTestIncomplete();
        $this->assertFalse($this->context->isCreate());
    }

    /**
     * Test val method
     */
    public function testVal()
    {
        $this->markTestIncomplete();
        $this->assertEquals($this->context->val('test_string'), 'Some Text');
        $this->assertEquals($this->context->val('test_int'), 3);
        $this->assertEquals($this->context->val('test_bool'), true);
    }

    /**
     * Test isRequired method
     */
    public function testIsRequired()
    {
        $this->markTestIncomplete();
        $this->assertFalse($this->context->isRequired('id'));
        $this->assertFalse($this->context->isRequired('foo'));
        $this->assertFalse($this->context->isRequired('baz'));
    }

    /**
     * Test fieldNames method
     */
    public function testFieldNames()
    {
        $this->markTestIncomplete();
        $result = $this->context->fieldNames();
        $expected = ['test.test_string', 'test.test_int', 'test.test_bool'];

        $this->assertEquals($expected, $result);
    }

    /**
     * Test attributes method
     */
    public function testAttributes()
    {
        $this->markTestIncomplete('Test field with attributes');
        $this->assertEmpty($this->context->attributes('test_string'));

    }

    /**
     * Test  method
     */
    public function testHasError()
    {
        $this->assertFalse($this->context->hasError('test_string'));

        $this->markTestIncomplete('Test field with errors');
    }

    /**
     * Test  method
     */
    public function testError()
    {
        $this->assertEmpty($this->context->error('test_string'));

        $this->markTestIncomplete('Test field with errors');
    }
}