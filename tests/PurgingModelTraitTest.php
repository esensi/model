<?php

use \Esensi\Model\Model;
use \Mockery;
use \PHPUnit_Framework_TestCase as PHPUnit;


/**
 * Tests for the Purging Model Trait
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <wishlist@emersonmedia.com>
 * @copyright 2014 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 */
class PurgingModelTraitTest extends PHPUnit {

    /**
     * Set Up and Prepare Tests
     *
     * @return void
     */
    public function setUp()
    {
        // Mock the Model that uses the custom trait
        $this->model = Mockery::mock('ModelPurgingStub');
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
     * Enables Purging on mock
     *
     * @return void
     */
    protected function enablePurgingOnMock()
    {
        $this->model->shouldReceive('getPurging')
            ->atleast()
            ->times(1)
            ->andReturn(true);
    }

    /**
     * Disables Purging on mock
     *
     * @return void
     */
    protected function disablePurgingOnMock()
    {
        $this->model->shouldReceive('getPurging')
            ->atleast()
            ->times(1)
            ->andReturn(false);
    }

    /**
     * Test that Purging is enabled by default
     *
     * @return void
     */
    public function testPurgingEnabledByDefault()
    {
        $this->assertTrue($this->model->getPurging());
    }

    /**
     * Test that Purging can be enabled and disabled
     *
     * @return void
     */
    public function testSettingPurging()
    {
        // Disable Purging
        $this->model->setPurging(false);
        $this->assertFalse($this->model->getPurging());

        // Enable Purging
        $this->model->setPurging(true);
        $this->assertTrue($this->model->getPurging());
    }

    /**
     * Test that Purgeable attributes can be gotten
     *
     * @return void
     */
    public function testGettingPurgeableAttributes()
    {
        // Get the attributes
        $attributes = $this->model->getPurgeable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it returned the default value
        $this->assertContains('foo', $attributes);

        // Check that the count matches
        $this->assertCount(1, $attributes);
    }

    /**
     * Test that Purgeable attributes can be set
     *
     * @return void
     */
    public function testSettingPurgeableAttributes()
    {
        // Set the attributes
        $this->model->setPurgeable(['bar']);

        // Get the attributes
        $attributes = $this->model->getPurgeable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it returned the set value
        $this->assertContains('bar', $attributes);

        // Check that the count matches
        $this->assertCount(1, $attributes);
    }

    /**
     * Test that isPurgeable returns true when Purging is enabled
     * and the attribute is Purgeable
     *
     * @return void
     */
    public function testIsPurgeableReturnsTrue()
    {
        // Enable purging
        $this->enablePurgingOnMock();

        // Make sure getPurgeable is called and attribute is Purgeable
        $this->model->shouldReceive('getPurgeable')
            ->once()
            ->andReturn(['foo']);

        // Check that the attribute is Purgeable
        $this->assertTrue($this->model->isPurgeable('foo'));
    }

    /**
     * Test that non-Purgeable attribute is not Purgeable even
     * when Purging is enabled
     *
     * @return void
     */
    public function testIsPurgeableReturnsFalseWhenNotSet()
    {
        // Enable purging
        $this->enablePurgingOnMock();

        // Make sure getPurgeable is called and attribute is not Purgeable
        $this->model->shouldReceive('getPurgeable')
            ->once()
            ->andReturn(['bar']);

        // Check that the attribute is not Purgeable
        $this->assertFalse($this->model->isPurgeable('foo'));
    }

    /**
     * Test that Purgeable attribute is not Purgeable when
     * Purging is disabled
     *
     * @return void
     */
    public function testIsPurgeableReturnsFalseWhenDisabled()
    {
        // Disable purging
        $this->disablePurgingOnMock();

        // Make sure getPurgeable is called and attribute is Purgeable
        $this->model->shouldReceive('getPurgeable')
            ->andReturn(['foo']);

        // Check that the attribute is not Purgeable
        $this->assertFalse($this->model->isPurgeable('foo'));
    }

    /**
     * Test that all Purgeable attributes are hashed
     *
     * @return void
     */
    public function testPurgeAttributes()
    {
        $attributes = [ 'foo' => 'fighters', 'bar' => 'soap' ];
        $purgeables = [ '_hidden' => 'field', 'baz_confirmation' => 'confirmed' ];
        $this->model->setPurgeable(array_keys($purgeables));
        $this->model->unguard();
        $this->model->fill(array_merge($attributes, $purgeables));

        // Do it
        $this->model->purgeAttributes();

        // Check the purgeable attributes were purged
        $this->assertSame($attributes, $this->model->getAttributes());
    }

}

/**
 * Model Stub for Purging Tests
 */
class ModelPurgingStub extends Model {

    /**
     * The attributes to purge before saving
     *
     * @var array
     */
    protected $purgeable = ['foo'];

}
