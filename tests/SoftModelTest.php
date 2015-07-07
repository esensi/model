<?php

use Esensi\Model\SoftModel;
use PHPUnit_Framework_TestCase as PHPUnit;

/**
 * Tests for the Soft Model
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2014 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 */
class SoftModelTest extends PHPUnit
{
    /**
     * Set Up and Prepare Tests.
     */
    public function setUp()
    {
        // Mock the Model that uses the custom traits
        $this->model = Mockery::mock('SoftModelStub');
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
     * Test that the Model uses the traits and in the right order.
     */
    public function testModelUsesTraits()
    {
        // Get the traits off the model
        $traits = function_exists('class_uses_recursive') ?
            class_uses_recursive(get_class($this->model)) : class_uses(get_class($this->model));

        // Check Model uses the Soft Deleting trait
        $this->assertContains('Esensi\Model\Traits\SoftDeletingModelTrait', $traits);
    }

    /**
     * Test that the Model implements the interfaces.
     */
    public function testModelImplementsInterfaces()
    {
        // Check Model implements the Soft Deleting interface
        $this->assertInstanceOf('\Esensi\Model\Contracts\SoftDeletingModelInterface', $this->model);
    }

    /**
     * Test that the Model returns deleted_at from getDates().
     */
    public function testGetDatesReturnsTimestamps()
    {
        // Check that getDeletedAtColumn is called in getDates
        $this->model->shouldReceive('getDeletedAtColumn')
            ->once()
            ->andReturn('deleted_at');

        // Get the date attributes
        $dates = $this->model->getDates();
        $class = get_class($this->model);

        // Check that deleted_at is a date attribute
        $this->assertContains('deleted_at', $dates);

        // Check that created_at is a date attribute
        $this->assertContains($class::CREATED_AT, $dates);

        // Check that created_at is date attribute
        $this->assertContains($class::UPDATED_AT, $dates);
    }

    /**
     * Test that the Model merges $dates attribute when returning getDates().
     */
    public function testGetDatesMergedDates()
    {
        $this->assertContains('foo', $this->model->getDates());
    }

}

/**
 * Soft Model Stub for Model Tests
 */
class SoftModelStub extends SoftModel
{
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['foo'];

}
