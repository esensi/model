<?php

use Esensi\Model\Observers\HashingModelObserver;
use PHPUnit_Framework_TestCase as PHPUnit;

/**
 * Tests for Hashing Model Observer
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2014 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 */
class HashingModelObserverTest extends PHPUnit
{
    /**
     * Set Up and Prepare Tests.
     */
    public function setUp()
    {
        // Create a new instance of the HashingModelObserver
        $this->observer = new HashingModelObserver;

        // Mock the model that implements the HashingModelTrait
        $this->model = Mockery::mock('\Esensi\Model\Model');
    }

    /**
     * Tear Down and Clean Up Tests.
     */
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * Enables hashing on mock.
     */
    protected function enableHashingOnMock()
    {
        // Enable hashing on mock
        $this->model->shouldReceive('getHashing')
            ->andReturn(true);
    }

    /**
     * Disables hashing on mock.
     */
    protected function disableHashingOnMock()
    {
        // Enable hashing on mock
        $this->model->shouldReceive('getHashing')
            ->andReturn(false);
    }

    /**
     * Test that hashAttributes() is called when getHashing() returns true.
     */
    public function testHashingIsPerformedWhenEnabled()
    {
        $this->enableHashingOnMock();

        // Check that hashing is performed
        $this->model->shouldReceive('hashAttributes')
            ->once();

        // Run it
        $this->observer->creating($this->model);
    }

    /**
     * Test that hashAttributes() is not called when getHashing() returns false.
     */
    public function testHashingIsNotPerformedWhenDisabled()
    {
        $this->disableHashingOnMock();

        // Check that hashing is not performed
        $this->model->shouldReceive('hashAttributes')
            ->never();

        // Run it
        $this->observer->creating($this->model);
    }

    /**
     * Test that performHashing() is called when creating().
     */
    public function testHashingIsPerformedWhenCreating()
    {
        $this->enableHashingOnMock();

        // Check that hashing is performed
        $this->model->shouldReceive('hashAttributes')
            ->once();

        // Run it
        $this->observer->updating($this->model);
    }

    /**
     * Test that performHashing() is called when updating().
     */
    public function testHashingIsPerformedWhenUpdating()
    {
        $this->enableHashingOnMock();

        // Check that hashing is performed
        $this->model->shouldReceive('hashAttributes')
            ->once();

        // Run it
        $this->observer->updating($this->model);
    }

}
