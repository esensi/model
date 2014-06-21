<?php

use \Esensi\Model\Model;
use \Mockery;
use \PHPUnit_Framework_TestCase as PHPUnit;
use \Illuminate\Database\Eloquent\ModelNotFoundException;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use \Illuminate\Database\Eloquent\Relations\MorphTo;
use \Illuminate\Database\Connection;
use \Illuminate\Database\ConnectionResolverInterface;
use \Illuminate\Database\Query\Grammars\Grammar;
use \Illuminate\Database\Query\Processors\Processor;

/**
 * Tests for the Relating Model Trait
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <wishlist@emersonmedia.com>
 * @copyright 2014 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 */
class RelatingModelTraitTest extends PHPUnit {

    /**
     * Set Up and Prepare Tests
     *
     * @return void
     */
    public function setUp()
    {
        // Mock the Model that uses the custom trait
        $this->model = Mockery::mock('ModelRelatingStub');
        $this->model->makePartial();
    }

    /**
     * Tear Down and Clean Up Tests
     *
     * @return void
     */
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * Test that getRelationship returns a relationship config
     *
     * @return void
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
     * the relationship does not exist
     *
     * @expectedException \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return void
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
     * Test that isRelationship returns true when relationship exists
     *
     * @return void
     */
    public function testIsRelationshipReturnsTrue()
    {
        $this->assertTrue($this->model->isRelationship('foo'));
    }

    /**
     * Test that isRelationship returns false when relationship does not exist
     *
     * @return void
     */
    public function testIsRelationshipReturnsFalse()
    {
        $this->assertFalse($this->model->isRelationship('baz'));
    }

    /**
     * Test that callRelationship returns the relationship
     *
     * @return void
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

}

/**
 * Model Stub for Relationships Tests
 */
class ModelRelatingStub extends Model {

    /**
     * Indicates if the model exists.
     *
     * @var bool
     */
    public $exists = false;

    /**
     * Relationships that the model should set up
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
        ]
    ];

}

/**
 * Foo Model Stub for Relationship Tests
 */
class FooModelStub extends Model {

}

/**
 * Bar Model Stub for Relationship Tests
 */
class BarModelStub extends Model {

}
