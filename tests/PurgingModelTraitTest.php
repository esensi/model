<?php

use Esensi\Model\Model;
use PHPUnit_Framework_TestCase as PHPUnit;

/**
 * Tests for the Purging Model Trait
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2014 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 */
class PurgingModelTraitTest extends PHPUnit
{
    /**
     * Set Up and Prepare Tests.
     */
    public function setUp()
    {
        // Mock the Model that uses the custom trait
        $this->model = Mockery::mock('ModelPurgingStub');
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
     * Enables Purging on mock.
     */
    protected function enablePurgingOnMock()
    {
        $this->model->shouldReceive('getPurging')
            ->atleast()
            ->times(1)
            ->andReturn(true);
    }

    /**
     * Disables Purging on mock.
     */
    protected function disablePurgingOnMock()
    {
        $this->model->shouldReceive('getPurging')
            ->atleast()
            ->times(1)
            ->andReturn(false);
    }

    /**
     * Test that Purging is enabled by default.
     */
    public function testPurgingEnabledByDefault()
    {
        $this->assertTrue($this->model->getPurging());
    }

    /**
     * Test that Purging can be enabled and disabled.
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
     * Test that Purgeable attributes can be gotten.
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
     * Test that Purgeable attributes can be set.
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
        $this->assertNotContains('foo', $attributes);
        $this->assertContains('bar', $attributes);

        // Check that the count matches
        $this->assertCount(1, $attributes);
    }

    /**
     * Test that a single Purgeable attribute can be added.
     */
    public function testAddingSinglePurgeableAttribute()
    {
        // Add a single attribute
        $this->model->addPurgeable('bar');

        // Get the attributes
        $attributes = $this->model->getPurgeable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it returned the set value
        $this->assertContains('foo', $attributes);
        $this->assertContains('bar', $attributes);

        // Check that the count matches
        $this->assertCount(2, $attributes);
    }

    /**
     * Test that multiple Purgeable attribute can be added simultaneously.
     */
    public function testAddingMultiplePurgeableAttributes()
    {
        // Add multiple attributes
        $this->model->addPurgeable('bar', 'baz');

        // Get the attributes
        $attributes = $this->model->getPurgeable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it returned the set values
        $this->assertContains('foo', $attributes);
        $this->assertContains('bar', $attributes);
        $this->assertContains('baz', $attributes);

        // Check that the count matches
        $this->assertCount(3, $attributes);
    }

    /**
     * Test that a single Purgeable attribute can be removed.
     */
    public function testRemovingSinglePurgeableAttribute()
    {
        // Set the attributes
        $this->model->setPurgeable(['foo', 'bar']);

        // Remove a single attribute
        $this->model->removePurgeable('bar');

        // Get the attributes
        $attributes = $this->model->getPurgeable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it did not return the unset value
        $this->assertContains('foo', $attributes);
        $this->assertNotContains('bar', $attributes);

        // Check that the count matches
        $this->assertCount(1, $attributes);
    }

    /**
     * Test that multiple Purgeable attribute can be removed simultaneously.
     */
    public function testRemovingMultiplePurgeableAttributes()
    {
        // Set the attributes
        $this->model->setPurgeable(['foo', 'bar', 'baz']);

        // Remove multiple attributes
        $this->model->removePurgeable('bar', 'baz');

        // Get the attributes
        $attributes = $this->model->getPurgeable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it did not returned the unset values
        $this->assertContains('foo', $attributes);
        $this->assertNotContains('bar', $attributes);
        $this->assertNotContains('baz', $attributes);

        // Check that the count matches
        $this->assertCount(1, $attributes);
    }

    /**
     * Test that removing all Purgeable attributes returns an empty array.
     */
    public function testRemovingAllPurgeableAttributes()
    {
        // Remove all attributes
        $this->model->removePurgeable('foo');

        // Get the attributes
        $attributes = $this->model->getPurgeable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it did not returned the unset values
        $this->assertNotContains('foo', $attributes);

        // Check that the count matches
        $this->assertEmpty($attributes);
    }

    /**
     * Test that Purgeable attributes can be merged.
     */
    public function testMergingPurgeableAttributes()
    {
        // Merge the attributes
        $this->model->mergePurgeable(['bar']);

        // Get the attributes
        $attributes = $this->model->getPurgeable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it returned the merged values
        $this->assertContains('foo', $attributes);
        $this->assertContains('bar', $attributes);

        // Check that the count matches
        $this->assertCount(2, $attributes);
    }

    /**
     * Test that isPurgeable returns true when Purging is enabled
     * and the attribute is Purgeable.
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
     * when Purging is enabled.
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
     * Purging is disabled.
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
     * Test that all Purgeable attributes are hashed.
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

    /**
     * Test that saveWithPurging calls purgeAttributes even when disabled.
     */
    public function testSaveWithPurging()
    {
        // Disable purging
        $this->model->setPurging(false);

        // Test that it is indeed enabled
        $this->model->shouldReceive('setPurging')
            ->once()
            ->with(true);

        // Mock save
        $this->model->shouldReceive('save')
            ->once()
            ->andReturn(true);

        // Test that it re-disabled
        $this->model->shouldReceive('setPurging')
            ->once()
            ->with(false);

        // Do it
        $response = $this->model->saveWithPurging();

        // Check that it returned true
        $this->assertTrue($response);

        // Check that purging is still disabled
        $this->assertFalse($this->model->getPurging());
    }

    /**
     * Test that saveWithoutPurging does not call purgeAttributes even when enabled.
     */
    public function testSaveWithOutPurging()
    {
        // Enable purging
        $this->model->setPurging(true);

        // Test that it is indeed disabled
        $this->model->shouldReceive('setPurging')
            ->once()
            ->with(false);

        // Mock save
        $this->model->shouldReceive('save')
            ->once()
            ->andReturn(true);

        // Test that it re-enabled
        $this->model->shouldReceive('setPurging')
            ->once()
            ->with(true);

        // Do it
        $response = $this->model->saveWithOutPurging();

        // Check that it returned true
        $this->assertTrue($response);

        // Check that purging is still enabled
        $this->assertTrue($this->model->getPurging());
    }

}

/**
 * Model Stub for Purging Tests
 */
class ModelPurgingStub extends Model
{
    /**
     * The attributes to purge before saving
     *
     * @var array
     */
    protected $purgeable = ['foo'];

}
