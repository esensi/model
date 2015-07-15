<?php

use Esensi\Model\Model;
use PHPUnit_Framework_TestCase as PHPUnit;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;

/**
 * Tests for the Relating Model Trait
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2014 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 */
class RelatingModelTraitTest extends PHPUnit
{
    /**
     * Set Up and Prepare Tests.
     */
    public function setUp()
    {
        // Mock the Model that uses the custom trait
        $this->model = Mockery::mock('ModelRelatingStub');
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
     * Test that getRelationship returns a relationship config.
     */
    public function testGettingRelationshipThatExists()
    {
        $this->model->shouldReceive('isRelationship')
            ->once()
            ->with('foo')
            ->andReturn(true);

        $relationship = $this->model->getRelationship('foo');
        $this->assertSame( $relationship, ['belongsTo', 'FooModelStub']);
    }

    /**
     * Test that getRelationship throws an exception when
     * the relationship does not exist.
     *
     * @expectedException \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function testGettingRelationshipThatDoesNotExist()
    {
        $this->model->shouldReceive('isRelationship')
            ->once()
            ->with('foo')
            ->andReturn(false);

        $this->model->getRelationship('foo');
    }

    /**
     * Test that getPivotAttributes returns the attributes
     * for a many-to-many relationship.
     */
    public function testGettingPivotAttributes()
    {
        $attributes = $this->model->getPivotAttributes('many');
        $this->assertEquals($attributes, ['foo', 'timestamps']);
    }

    /**
     * Test that isRelationship returns true when relationship exists.
     */
    public function testIsRelationshipReturnsTrue()
    {
        $this->assertTrue($this->model->isRelationship('foo'));
    }

    /**
     * Test that isRelationship returns false when relationship does not exist.
     */
    public function testIsRelationshipReturnsFalse()
    {
        $this->assertFalse($this->model->isRelationship('baz'));
    }

    /**
     * Test that hasPivotAttributes returns true when relationship
     * has pivot attributes.
     */
    public function testHasPivotAttributesReturnsTrue()
    {
        $this->assertTrue($this->model->hasPivotAttributes('many'));
    }

    /**
     * Test that hasPivotAttributes returns false when relationship
     * does not have pivot attributes.
     */
    public function testHasPivotAttributesReturnsFalse()
    {
        $this->assertFalse($this->model->hasPivotAttributes('foo'));
    }

    /**
     * Test that callRelationship returns the relationship.
     */
    public function testCallRelationship()
    {
        // Mock the Connection
        $model = new ModelRelatingStub();
        $model->setConnectionResolver($resolver = Mockery::mock('\Illuminate\Database\ConnectionResolverInterface'));
        $resolver->shouldReceive('connection')->andReturn(Mockery::mock('\Illuminate\Database\Connection'));
        $model->getConnection()->shouldReceive('getQueryGrammar')->andReturn(Mockery::mock('\Illuminate\Database\Query\Grammars\Grammar'));
        $model->getConnection()->shouldReceive('getPostProcessor')->andReturn(Mockery::mock('\Illuminate\Database\Query\Processors\Processor'));

        // Check that belongsTo works using dynamic calls
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Relations\BelongsTo', $model->foo());

        // Check that morphTo works using dynamic calls
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Relations\MorphTo', $model->bar());
    }

    /**
     * Test that scopeWithout removes array dot keys.
     */
    public function testScopeWithoutRemovesArrayDotKeys()
    {
        $model = new ModelRelatingStub();

        // Make sure deep unsetting of relationships does not affect deep set relationship's high level
        $relationships = $model->with('foo.bar')->without('foo.bar')->getEagerLoads();
        $keys = array_keys(array_dot($relationships));
        $this->assertContains('foo', $keys);
        $this->assertNotContains('foo.bar', $keys);

        // Make sure high level setting can be unset with explicit low level setting
        $relationships = $model->with('foo.bar')->without('foo', 'foo.bar')->getEagerLoads();
        $keys = array_keys(array_dot($relationships));
        $this->assertNotContains('foo', $keys);
        $this->assertNotContains('foo.bar', $keys);

        // Make sure high level unsetting maintains deep relationships
        $relationships = $model->with('foo.bar')->without('foo')->getEagerLoads();
        $keys = array_keys(array_dot($relationships));
        $this->assertContains('foo', $keys);
        $this->assertContains('foo.bar', $keys);

        // Make sure high level relationships are not unset by low level
        $relationships = $model->with('foo')->without('foo.bar')->getEagerLoads();
        $keys = array_keys(array_dot($relationships));
        $this->assertContains('foo', $keys);
        $this->assertNotContains('foo.bar', $keys);
    }

}

/**
 * Model Stub for Relationships Tests
 */
class ModelRelatingStub extends Model
{
    /**
     * Indicates if the model exists.
     *
     * @var boolean
     */
    public $exists = false;

    /**
     * Relationships that the model should set up.
     *
     * @var array
     */
    protected $relationships = [

        'foo' => [
            'belongsTo',
            'FooModelStub'
        ],

        'bar' => [
            'morphTo',
            'BarModelStub',
        ],

        'many' => [
            'belongsToMany',
            'ManyModelStub',
        ],
    ];

    /**
     * Extra attributes to be added to pivot relationships.
     *
     * @var array
     */
    protected $relationshipPivots = [

        'many' => [ 'foo', 'timestamps' ],
    ];
}

/**
 * Foo Model Stub for Relationship Tests
 */
class FooModelStub extends Model
{

    /**
     * Relationships that the model should set up.
     *
     * @var array
     */
    protected $relationships = [

        'bar' => [
            'belongsTo',
            'BarModelStub',
        ],
    ];
}

/**
 * Bar Model Stub for Relationship Tests
 */
class BarModelStub extends Model
{

}

/**
 * Many Model Stub for Relationship Tests
 */
class ManyModelStub extends Model
{

}
