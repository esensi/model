<?php

use \Carbon\Carbon;
use \Esensi\Model\Model;
use \Illuminate\Database\Connection;
use \Illuminate\Database\ConnectionResolverInterface;
use \Illuminate\Database\Query\Grammars\Grammar;
use \Illuminate\Database\Query\Processors\Processor;
use \Mockery;
use \PHPUnit_Framework_TestCase as PHPUnit;

/**
 * Tests for the Purging Model Trait
 *
 * @package Esensi\Model
 * @author Diego Caprioli <diego@emersonmedia.com>
 * @author Dnaiel LaBarge <dalabarge@emersonmedia.com>
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
        $count = count($this->model->tmpAttributes);
        $this->assertCount($count, $attributes);
    }

    /**
     * Test that Jugglable attributes can be set.
     */
    public function testSettingJugglableAttributes()
    {
        // Set the attributes
        $this->model->setJugglable(['foo' => 'bar']);

        // Get the attributes
        $attributes = $this->model->getJugglable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it returned the set value
        $this->assertContains('foo', array_keys($attributes));

        // Check that the count matches
        $this->assertCount(1, $attributes);
    }

    /**
     * Test that a single Jugglable attribute can be added.
     */
    public function testAddingSingleJugglableAttribute()
    {
        // Add a single attribute
        $this->model->addJugglable('foo', 'bar');

        // Get the attributes
        $attributes = $this->model->getJugglable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it returned the set value
        $this->assertContains('foo', array_keys($attributes));
        $this->assertEquals('bar', $attributes['foo']);

        // Check that the count matches
        $count = count($this->model->tmpAttributes) + 1;
        $this->assertCount($count, $attributes);
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
        $count = count($this->model->tmpAttributes) - 1;
        $this->assertCount($count, $attributes);
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
        $count = count($this->model->tmpAttributes) - 2;
        $this->assertCount($count, $attributes);

        // Remove multiple attributes
        $this->model->removeJugglable(['myFloat', 'myArray']);

        // Get the attributes
        $attributes = $this->model->getJugglable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it did not returned the unset values
        $this->assertContains('myDateTime', array_keys($attributes));
        $this->assertNotContains('myString', array_keys($attributes));
        $this->assertNotContains('myDate', array_keys($attributes));

        // Check that the count matches
        $count = count($this->model->tmpAttributes) - 4;
        $this->assertCount($count, $attributes);
    }

    /**
     * Test that removing all Jugglable attributes returns an empty array.
     */
    public function testRemovingAllJugglableAttributes()
    {
        // Remove all attributes
        $this->model->removeJugglable( array_keys($this->model->tmpAttributes) );

        // Get the attributes
        $attributes = $this->model->getJugglable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that the count matches
        $this->assertEmpty($attributes);
    }

    /**
     * Test that Jugglable attributes can be merged.
     * Depends on testRemovingAllJugglableAttributes() being ran previously.
     */
    public function testMergingJugglableAttributes()
    {
        // Merge the attributes
        $this->model->mergeJugglable([
            'foo' => 'integer',
            'bar' => 'boolean',
        ]);

        // Get the attributes
        $attributes = $this->model->getJugglable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it returned the merged values
        $this->assertContains('foo', array_keys($attributes));
        $this->assertContains('bar', array_keys($attributes));

        // Check that the count matches
        $count = count($this->model->tmpAttributes) + 2;
        $this->assertCount($count, $attributes);
    }

    /**
     * Test that isJugglable returns true when Juggling is enabled
     * and the attribute is Jugglable.
     */
    public function testIsJugglableReturnsTrue()
    {
        // Enable juggling
        $this->model->setJuggling(true);

        // Check that the attribute is Jugglable
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
        $this->assertFalse($this->model->isJugglable('foo'));
    }

    /**
     * Test that Jugglable attribute is not Jugglable when
     * Juggling is disabled.
     */
    public function testIsJugglableReturnsFalseWhenDisabled()
    {
        // Disable juggling
        $this->model->setJuggling(false);

        // Check that the attribute is not Jugglable
        $this->assertFalse($this->model->isJugglable('myString'));
    }

    /**
     * Test that all Jugglable attributes are juggled.
     */
    public function testJuggleAttributes()
    {
        $this->model->setConnectionResolver($resolver = Mockery::mock('\Illuminate\Database\ConnectionResolverInterface'));
        $resolver->shouldReceive('connection')->andReturn(Mockery::mock('\Illuminate\Database\Connection'));
        $grammar = Mockery::mock('\Illuminate\Database\Query\Grammars\Grammar');
        $grammar->shouldReceive('getDateFormat')->andReturn('Y-m-d H:i:s');
        $this->model->getConnection()->shouldReceive('getQueryGrammar')->andReturn($grammar);
        $this->model->getConnection()->shouldReceive('getPostProcessor')->andReturn(Mockery::mock('\Illuminate\Database\Query\Processors\Processor'));

        // Make sure we are dealing with an empty model
        $this->assertEmpty( $this->model->getAttributes() );

        // Enable juggling
        $this->model->setJuggling(true);

        // Set attributes into the model using fill
        foreach ($this->model->tmpAttributes as $key => $value)
        {
            $this->model->{$key} = $value;
        }

        // Get the attributes
        $attributes = $this->model->getAttributes();

        // Check that the attributes count matches
        $count = count($this->model->tmpAttributes);
        $this->assertCount($count, $attributes);

        // Check that the attributes are set and return the correct types
        $this->assertInternalType('string', $this->model->myString);
        $this->assertInstanceOf('\Carbon\Carbon', $this->model->myDate);
        $this->assertInternalType('string', $this->model->myDateTime);
        $this->assertInternalType('integer', $this->model->myTimestamp);
        $this->assertInternalType('integer', $this->model->myInt);
        $this->assertInternalType('integer', $this->model->myInteger);
        $this->assertInternalType('boolean', $this->model->myBool);
        $this->assertInternalType('boolean', $this->model->myBoolean);
        $this->assertInternalType('float', $this->model->myDouble);
        $this->assertInternalType('float', $this->model->myFloat);
        $this->assertInternalType('array', $this->model->myArray);
    }

}

/**
 * Model Stub for Juggling Tests
 */
class ModelJugglingStub extends Model {

    /**
     * Indicates if the model exists.
     *
     * @var boolean
     */
    public $exists = false;

    /**
     * The attributes to type juggle
     *
     * @var array
     */
    protected $jugglable = [
        'myString'    => 'string',
        'myDate'      => 'date',
        'myDateTime'  => 'dateTime',
        'myTimestamp' => 'timestamp',
        'myInt'       => 'int',
        'myInteger'   => 'integer',
        'myBool'      => 'bool',
        'myBoolean'   => 'boolean',
        'myDouble'    => 'double',
        'myFloat'     => 'float',
        'myArray'     => 'array',
    ];

    /**
     * The temporary attributes that holds the values used
     * in the tests to set the attributes in the object.
     * Make sure the keys align with $jugglabe property on this stub.
     *
     * @var array
     */
    public $tmpAttributes = [
        'myString'    => 'Hello world',
        'myDate'      => '2014-01-01',
        'myDateTime'  => '2014-01-01',
        'myTimestamp' => '2014-01-01',
        'myInt'       => '123',
        'myInteger'   => '123',
        'myBool'      => '1',
        'myBoolean'   => '1',
        'myDouble'    => '1.12',
        'myFloat'     => '1.12',
        'myArray'     => 'elem',
    ];

}
