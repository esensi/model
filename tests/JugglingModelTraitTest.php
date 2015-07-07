<?php

use Carbon\Carbon;
use Esensi\Model\Model;
use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use PHPUnit_Framework_TestCase as PHPUnit;

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
class JugglingModelTraitTest extends PHPUnit
{
    /**
     * Set Up and Prepare Tests.
     */
    public function setUp()
    {
        // Mock the Model that uses the custom trait
        $this->model = Mockery::mock('ModelJugglingStub');
        $this->model->makePartial();

        // For date operations make sure we're in EST
        date_default_timezone_set('America/New_York');
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
     * Test that setting invalid juggle types throws exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testSettingInvalidJuggleTypeThrowsException()
    {
        $this->model->setJugglable(['foo' => 'foo']);
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
     * Test that adding an invalid juggle type throws exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testAddingInvalidJuggleTypeThrowsException()
    {
        $this->model->addJugglable('foo', 'foo');
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
     * Test that merging invalid juggle types throws exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMergingInvalidJuggleTypeThrowsException()
    {
        $this->model->mergeJugglable(['foo' => 'foo']);
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
     * Test that the valid juggle types return true.
     */
    public function testIsJuggleTypeReturnsTrueForValidTypes()
    {
        $types = ['boolean', 'integer', 'float', 'string', 'array', 'date', 'datetime', 'timestamp', 'bar'];
        foreach($types as $type)
        {
            $this->assertTrue($this->model->isJuggleType( $type ));
        }
    }

    /**
     * Test that the invalid juggle type return false.
     */
    public function testIsJuggleTypeReturnsFalseForInvalidType()
    {
        $this->model->shouldReceive('buildJuggleMethod')
            ->once()
            ->with('foo')
            ->andReturn('juggleFoo');

        $this->assertFalse($this->model->isJuggleType( 'foo' ));
    }

    /**
     * Test that the valid juggle types return true.
     */
    public function testCheckJuggleTypeReturnsTrueForValidTypes()
    {
        $this->model->shouldReceive('isJuggleType')
            ->once()
            ->with('foo')
            ->andReturn(true);

        $this->assertTrue($this->model->checkJuggleType( 'foo' ));
    }

    /**
     * Test that an invalid juggle type return false.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testCheckJuggleTypeReturnsFalseForInvalidType()
    {
        $this->model->shouldReceive('isJuggleType')
            ->once()
            ->with('foo')
            ->andReturn(false);

        $this->assertFalse($this->model->checkJuggleType( 'foo' ));
    }

    /**
     * Test that the juggle method builder returns normalized method names.
     */
    public function testBuildJuggleMethod()
    {
        $map = [
            'juggleBoolean' => ['bool', 'boolean', 'Bool', 'Boolean'],
            'juggleInteger' => ['int', 'integer', 'Int', 'Integer'],
            'juggleFloat' => ['float', 'double', 'Float', 'Double'],
            'juggleString' => ['string', 'String'],
            'juggleArray' => ['array', 'Array'],
            'juggleDate' => ['date', 'Date'],
            'juggleDateTime' => ['datetime', 'dateTime', 'date_time', 'DateTime'],
            'juggleTimestamp' => ['timestamp', 'Timestamp'],
            'juggleFooBar' => ['foo_bar', 'fooBar', 'FooBar'],
        ];

        foreach($map as $method => $types)
        {
            foreach($types as $type)
            {
                $this->assertEquals($method, $this->model->buildJuggleMethod($type));
            }
        }
    }

    /**
     * Test that getJuggleType returns the value from the jugglable array.
     */
    public function testGetJuggleType()
    {
        // Make sure the attributes are gotten with getJugglable()
        $this->model->shouldReceive('getJugglable')
            ->once()
            ->andReturn(['foo' => 'bar']);

        // Check the type
        $type = $this->model->getJuggleType('foo');
        $this->assertEquals('bar', $type);
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

        // Make sure juggleAttributes calls juggleAttribute iteratively
        $count = count($this->model->tmpAttributes);
        $this->model->shouldReceive('juggleAttribute')
            ->times($count);

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

    /**
     * Test that juggling an invalid juggle type throws exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testJugglingInvalidJuggleTypeThrowsException()
    {
        $this->model->juggle('foo', 'foo');
    }

    /**
     * Test that juggleDate returns Carbon date object.
     */
    public function testJuggleDate()
    {
        // A date string should cast to a Carbon object
        $date = '1970-01-01';
        $carbon = $this->model->juggleDate( $date );
        $this->assertInstanceOf('\Carbon\Carbon', $carbon );
        $this->assertEquals($carbon->format('Y-m-d'),  $date );

        // A Carbon object should return the same Carbon date on a second call
        $carbon2 = $this->model->juggledate( $carbon );
        $this->assertEquals($carbon, $carbon2 );
    }

    /**
     * Test that juggleDateTime returns ISO standard for datetime.
     */
    public function testJuggleDateTime()
    {
        $datetime = $this->model->juggleDateTime( '1970-01-01' );
        $this->assertEquals('1970-01-01 00:00:00', $datetime);
    }

    /**
     * Test that juggleTimestamp returns an Unix timestamp as an integer.
     */
    public function testJuggleTimestamp()
    {
        $timestamp = $this->model->juggleTimestamp( '1970-01-01' );
        $this->assertInternalType('integer', $timestamp);
        $this->assertEquals(18000, $timestamp);
    }

    /**
     * Test that juggleBoolean returns boolean values for boolean like values.
     *
     * Note that settype("false", "boolean") returns true which is why string("true")
     * and string("false") have been left out of this test.
     * @link http://php.net/manual/en/language.types.boolean.php#language.types.boolean.casting
     */
    public function testJuggleBoolean()
    {
        // Test true values
        foreach([true, 1, '1'] as $value)
        {
            $boolean = $this->model->juggleBoolean( $value );
            $this->assertInternalType('boolean', $boolean);
            $this->assertTrue($boolean);
        }

        // Test false values
        foreach([false, 0, '0'] as $value)
        {
            $boolean = $this->model->juggleBoolean( $value );
            $this->assertInternalType('boolean', $boolean);
            $this->assertFalse($boolean);
        }
    }

    /**
     * Test that juggleInteger returns integer values.
     */
    public function testJuggleInteger()
    {
        $integer = $this->model->juggleInteger( '1' );
        $this->assertInternalType('integer', $integer);
        $this->assertEquals(1, $integer);

        $integer = $this->model->juggleInteger( '1 large pizza' );
        $this->assertInternalType('integer', $integer);
        $this->assertEquals(1, $integer);
    }

    /**
     * Test that juggleFloat returns floating point values.
     */
    public function testJuggleFloat()
    {
        $float = $this->model->juggleFloat( '1.23456789' );
        $this->assertInternalType('float', $float);
        $this->assertEquals(1.23456789, $float);

        $float = $this->model->juggleFloat( 1/4 );
        $this->assertInternalType('float', $float);
        $this->assertEquals(0.25, $float);
    }

    /**
     * Test that juggleString returns string values.
     */
    public function testJuggleString()
    {
        $string = $this->model->juggleString( 1/4 );
        $this->assertInternalType('string', $string);
        $this->assertEquals('0.25', $string);
    }

    /**
     * Test that juggleArray returns array values.
     */
    public function testJuggleArray()
    {
        $array = $this->model->juggleArray( [] );
        $this->assertInternalType('array', $array);
        $this->assertEmpty($array);

        $array = $this->model->juggleArray( 'foo' );
        $this->assertInternalType('array', $array);
        $this->assertEquals(['foo'], $array);
    }
}

/**
 * Model Stub for Juggling Tests
 */
class ModelJugglingStub extends Model
{
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

    /**
     * Example custom juggle type.
     *
     * @param  mixed $value
     * @return string
     */
    protected function juggleBar( $value )
    {
        return 'bar';
    }
}
