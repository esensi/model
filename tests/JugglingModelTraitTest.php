<?php

use \Esensi\Model\Model;
use \Mockery;
use \PHPUnit_Framework_TestCase as PHPUnit;


/**
 * Tests for the Purging Model Trait
 *
 * @package Esensi\Model
 * @author Diego Caprioli <diego@emersonmedia.com>
 * @copyright 2014 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 */
class JugglingModelTraitTest extends PHPUnit {

    /**
     * Set Up and Prepare Tests.
     */
    public function setUp()
    {
        // Mock the Model that uses the custom trait
        $this->model = Mockery::mock('ModelJugglingStub');
        $this->model->makePartial();
    }

    /**
     * Tear Down and Clean Up Tests.
     */
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * Test that Juggling is enabled by default.
     */
    public function testJugglingEnabledByDefault()
    {
        $this->assertTrue($this->model->getJuggling());
    }

    /**
     * Test that Juggling can be enabled and disabled.
     */
    public function testSettingJuggling()
    {
        // Disable Juggling
        $this->model->setJuggling(false);
        $this->assertFalse($this->model->getJuggling());

        // Enable Juggling
        $this->model->setJuggling(true);
        $this->assertTrue($this->model->getJuggling());
    }

    /**
     * Test that Jugglable attributes can be gotten.
     */
    public function testGettingJugglableAttributes()
    {
        // Get the attributes
        $attributes = $this->model->getJugglable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that the count matches the expected stub
        $this->assertCount(9, $attributes);
    }

    /**
     * Test that Jugglable attributes can be set.
     */
    public function testSettingJugglableAttributes()
    {
        // Set the attributes
        $this->model->setJugglable(['myNewDatetime' => 'datetime']);

        // Get the attributes
        $attributes = $this->model->getJugglable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it returned the set value
        $this->assertNotContains('myDate', array_keys($attributes));
        $this->assertContains('myNewDatetime', array_keys($attributes));

        // Check that the count matches
        $this->assertCount(1, $attributes);
    }

    /**
     * Test that a single Jugglable attribute can be added.
     */
    public function testAddingSingleJugglableAttribute()
    {
        // Add a single attribute
        $this->model->addJugglable('myNewVar', 'string');

        // Get the attributes
        $attributes = $this->model->getJugglable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it returned the set value
        $this->assertContains('myString', array_keys($attributes));
        $this->assertContains('myArray', array_keys($attributes));
        $this->assertContains('myNewVar', array_keys($attributes));

        // Check that the count matches
        $this->assertCount(10, $attributes);
    }

    /**
     * Test that a single Jugglable attribute can be removed.
     */
    public function testRemovingSingleJugglableAttribute()
    {
        // Remove a single attribute
        $this->model->removeJugglable('myString');

        // Get the attributes
        $attributes = $this->model->getJugglable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it did not return the unset value
        $this->assertContains('myDate', array_keys($attributes));
        $this->assertNotContains('myString', array_keys($attributes));

        // Check that the count matches
        $this->assertCount(8, $attributes);
    }

    /**
     * Test that multiple Jugglable attribute can be removed simultaneously.
     */
    public function testRemovingMultipleJugglableAttributes()
    {
        // Remove multiple attributes
        $this->model->removeJugglable('myString', 'myDate');

        // Get the attributes
        $attributes = $this->model->getJugglable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it did not returned the unset values
        $this->assertContains('myDateTime', array_keys($attributes));
        $this->assertNotContains('myString', array_keys($attributes));
        $this->assertNotContains('myDate', array_keys($attributes));

        // Check that the count matches
        $this->assertCount(7, $attributes);

        // Remove multiple attributes
        $this->model->removeJugglable(['myFloat', 'myArray']);

        // Get the attributes
        $attributes = $this->model->getJugglable();

        // Check that it did not returned the unset values
        $this->assertContains('myDateTime', array_keys($attributes));
        $this->assertNotContains('myString', array_keys($attributes));
        $this->assertNotContains('myDate', array_keys($attributes));

        // Check that the count matches
        $this->assertCount(5, $attributes);
    }

    /**
     * Test that removing all Jugglable attributes returns an empty array.
     */
    public function testRemovingAllJugglableAttributes()
    {
        // Remove all attributes
        $this->model->removeJugglable('myString', 'myDate', 'myDateTime',
            'myTimestamp', 'myInteger', 'myBoolean', 'myDouble',
            'myFloat', 'myArray'
        );

        // Get the attributes
        $attributes = $this->model->getJugglable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that the count matches
        $this->assertEmpty($attributes);
    }

    /**
     * Test that Jugglable attributes can be merged.
     */
    public function testMergingJugglableAttributes()
    {
        // Merge the attributes
        $this->model->mergeJugglable([
            'myNewInt' => 'int',
            'myNewBool' => 'bool',
        ]);

        // Get the attributes
        $attributes = $this->model->getJugglable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it returned the merged values
        $this->assertContains('myNewInt', array_keys($attributes));
        $this->assertContains('myNewBool', array_keys($attributes));
        $this->assertContains('myString', array_keys($attributes));

        // Check that the count matches
        $this->assertCount(11, $attributes);
    }

    /**
     * Test that isJugglable returns true when Juggling is enabled
     * and the attribute is Jugglable.
     */
    public function testIsJugglableReturnsTrue()
    {
        // Enable juggling
        $this->model->setJuggling(true);

        // Check that the attribute is Purgeable
        $this->assertTrue($this->model->isJugglable('myString'));
    }

    /**
     * Test that non-Jugglable attribute is not Jugglable even
     * when Juggling is enabled.
     */
    public function testIsJugglableReturnsFalseWhenNotSet()
    {
        // Enable juggling
        $this->model->setJuggling(true);

        // Check that the attribute is not Jugglable
        $this->assertFalse($this->model->isPurgeable('myNotJugglableAtr'));
    }

    /**
     * Test that Jugglable attribute is not Jugglable when
     * Juggling is disabled.
     */
    public function testIsJugglableReturnsFalseWhenDisabled()
    {
        // Disable juggling
        $this->model->setJuggling(false);

        // Check that the attribute is not Purgeable
        $this->assertFalse($this->model->isJugglable('myString'));
    }

    /**
     * Test that all Jugglable attributes are juggled.
     */
    public function testJuggleAttributes()
    {

        // @todo: this test is not working... continue here

        /*
        //check that the attributes have not been set yet
        $this->assertEmpty($this->model->getAttributes());

        //Enable juggling
        $this->model->setJuggling(true);

        // set attributes into the model using fill
        foreach ($this->model->tmpAttributes as $key => $value)
        {
            $this->model->{$key} = $value;
        }

        $attributes = $this->model->getAttributes();
        var_dump($attributes);

        //check that the attributes have been set to the $attributes array
        $this->assertCount(9, $attributes);

        //check that the attributes are set using the correct types
        $this->assertInternalType('string', $attributes['myString']);
        $this->assertInstanceOf('\Carbon\Carbon', $attributes['myDate']);
        $this->assertInternalType('string', $attributes['myDatetime']);
        $this->assertInternalType('integer', $attributes['myTimestamp']);
        $this->assertInternalType('integer', $attributes['myInteger']);
        $this->assertInternalType('boolean', $attributes['myBoolean']);
        $this->assertInternalType('float', $attributes['myDouble']);
        $this->assertInternalType('float', $attributes['myFloat']);
        $this->assertInternalType('array', $attributes['myArray']);
        */

    }

}

/**
 * Model Stub for Juggling Tests
 */
class ModelJugglingStub extends Model {

    /**
     * The attributes to type juggle
     *
     * @var array
     */
    protected $jugglable = [
        'myString' => 'string',
        'myDate' => 'date',
        'myDateTime' => 'date_time',
        'myTimestamp' => 'timestamp',
        'myInteger' => 'integer',
        'myBoolean' => 'boolean',
        'myDouble' => 'double',
        'myFloat' => 'float',
        'myArray' => 'array',
    ];

    /**
     * The temporal var that holds the values to use
     * in the tests to set the attributes in the object
     * @var array
     */
    public $tmpAttributes = [
        'myString' => 'Hello world',
        'myDate' => '2014-01-01',
        'myDateTime' => '2014-01-01',
        'myTimestamp' => '2014-01-01',
        'myInteger' => '123',
        'myBoolean' => '1',
        'myDouble' => '1.12',
        'myFloat' => '1.12',
        'myArray' => 'elem',
    ];


}
