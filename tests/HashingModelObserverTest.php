<?php

use \Esensi\Model\Observers\HashingModelObserver;
use \Mockery;
use \PHPUnit_Framework_TestCase as PHPUnit;

/**
 * Tests for Hashing Model Observer
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <wishlist@emersonmedia.com>
 * @copyright 2014 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 */
class HashingModelObserverTest extends PHPUnit {

    /**
     * Set Up and Prepare Tests
     *
     * @return void
     */
    public function setUp()
    {
        // Create a new instance of the HashingModelObserver
        $this->observer = new HashingModelObserver;

        // Mock the model that implements the HashingModelTrait
        $this->model = Mockery::mock('\Esensi\Model\Model');
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
     * Test that hashAttributes() is called when getHashing() returns true
     *
     * @return void
     */
    public function testHashingIsPerformedWhenEnabled()
    {
        // Enable purging on mock
        $this->model->shouldReceive('getHashing')
            ->once()
            ->andReturn(true);

        // Check that purging is performed
        $this->model->shouldReceive('hashAttributes')
            ->once();

        // Run it
        $this->observer->creating($this->model);
    }

    /**
     * Test that hashAttributes() is not called when getHashing() returns false
     *
     * @return void
     */
    public function testHashingIsNotPerformedWhenDisabled()
    {
        // Enable purging on mock
        $this->model->shouldReceive('getHashing')
            ->once()
            ->andReturn(false);

        // Check that purging is not performed
        $this->model->shouldReceive('hashAttributes')
            ->never();

        // Run it
        $this->observer->creating($this->model);
    }

    /**
     * Test that performHashing() is called when creating()
     *
     * @return void
     */
    public function testHashingIsPerformedWhenCreating()
    {
        // Enable purging on mock
        $this->model->shouldReceive('getHashing')
            ->once()
            ->andReturn(true);

        // Check that purging is not performed
        $this->model->shouldReceive('hashAttributes')
            ->once();

        // Run it
        $this->observer->updating($this->model);
    }

    /**
     * Test that performHashing() is called when updating()
     *
     * @return void
     */
    public function testHashingIsPerformedWhenUpdating()
    {
        // Enable purging on mock
        $this->model->shouldReceive('getHashing')
            ->once()
            ->andReturn(true);

        // Check that purging is not performed
        $this->model->shouldReceive('hashAttributes')
            ->once();

        // Run it
        $this->observer->updating($this->model);
    }

}
