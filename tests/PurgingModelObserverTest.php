<?php

use Esensi\Model\Observers\PurgingModelObserver;
use PHPUnit_Framework_TestCase as PHPUnit;

/**
 * Tests for Purging Model Observer
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2014 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 */
class PurgingModelObserverTest extends PHPUnit
{
    /**
     * Set Up and Prepare Tests.
     */
    public function setUp()
    {
        // Create a new instance of the PurgingModelObserver
        $this->observer = new PurgingModelObserver;

        // Mock the model that implements the PurgingModelTrait
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
     * Enables purging on mock.
     */
    protected function enablePurgingOnMock()
    {
        // Enable purging on mock
        $this->model->shouldReceive('getPurging')
            ->andReturn(true);
    }

    /**
     * Disables purging on mock.
     */
    protected function disablePurgingOnMock()
    {
        // Enable purging on mock
        $this->model->shouldReceive('getPurging')
            ->andReturn(false);
    }

    /**
     * Test that purgeAttributes() is called when getPurging() returns true.
     */
    public function testPurgingIsPerformedWhenEnabled()
    {
        $this->enablePurgingOnMock();

        // Check that purging is performed
        $this->model->shouldReceive('purgeAttributes')
            ->once();

        // Run it
        $this->observer->creating($this->model);
    }

    /**
     * Test that purgeAttributes() is not called when getPurging() returns false.
     */
    public function testPurgingIsNotPerformedWhenDisabled()
    {
        $this->disablePurgingOnMock();

        // Check that purging is not performed
        $this->model->shouldReceive('purgeAttributes')
            ->never();

        // Run it
        $this->observer->creating($this->model);
    }

    /**
     * Test that performPurging() is called when creating().
     */
    public function testPurgingIsPerformedWhenCreating()
    {
        $this->enablePurgingOnMock();

        // Check that purging is performed
        $this->model->shouldReceive('purgeAttributes')
            ->once();

        // Run it
        $this->observer->updating($this->model);
    }

    /**
     * Test that performPurging() is called when updating().
     */
    public function testPurgingIsPerformedWhenUpdating()
    {
        $this->enablePurgingOnMock();

        // Check that purging is performed
        $this->model->shouldReceive('purgeAttributes')
            ->once();

        // Run it
        $this->observer->updating($this->model);
    }

}
