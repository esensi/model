<?php

use Esensi\Model\Model;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Hashing\BcryptHasher;
use PHPUnit_Framework_TestCase as PHPUnit;

/**
 * Tests for the Hashing Model Trait
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2014 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 */
class HashingModelTraitTest extends PHPUnit
{
    /**
     * Set Up and Prepare Tests.
     */
    public function setUp()
    {
        // Mock the Model that uses the custom trait
        $this->model = Mockery::mock('ModelHashingStub');
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
     * Enables Hashing on mock.
     */
    protected function enableHashingOnMock()
    {
        $this->model->shouldReceive('getHashing')
            ->atleast()
            ->times(1)
            ->andReturn(true);
    }

    /**
     * Disables Hashing on mock.
     */
    protected function disableHashingOnMock()
    {
        $this->model->shouldReceive('getHashing')
            ->atleast()
            ->times(1)
            ->andReturn(false);
    }

    /**
     * Sets the Hasher on mock.
     */
    public function setHasherOnMock()
    {
        $this->model->setHasher(new HasherStub());
    }

    /**
     * Test that Hashing is enabled by default.
     */
    public function testHashingEnabledByDefault()
    {
        $this->assertTrue($this->model->getHashing());
    }

    /**
     * Test that Hashing can be enabled and disabled.
     */
    public function testSettingHashing()
    {
        // Disable Hashing
        $this->model->setHashing(false);
        $this->assertFalse($this->model->getHashing());

        // Enable Hashing
        $this->model->setHashing(true);
        $this->assertTrue($this->model->getHashing());
    }

    /**
     * Test that Hashable attributes can be gotten.
     */
    public function testGettingHashableAttributes()
    {
        // Get the attributes
        $attributes = $this->model->getHashable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it returned the default value
        $this->assertContains('foo', $attributes);

        // Check that the count matches
        $this->assertCount(1, $attributes);
    }

    /**
     * Test that Hashable attributes can be set.
     */
    public function testSettingHashableAttributes()
    {
        // Set the attributes
        $this->model->setHashable(['bar']);

        // Get the attributes
        $attributes = $this->model->getHashable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it returned the set value
        $this->assertContains('bar', $attributes);

        // Check that the count matches
        $this->assertCount(1, $attributes);
    }

    /**
     * Test that a single Hashable attribute can be added.
     */
    public function testAddingSingleHashableAttribute()
    {
        // Add a single attribute
        $this->model->addHashable('bar');

        // Get the attributes
        $attributes = $this->model->getHashable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it returned the set value
        $this->assertContains('foo', $attributes);
        $this->assertContains('bar', $attributes);

        // Check that the count matches
        $this->assertCount(2, $attributes);
    }

    /**
     * Test that multiple Hashable attribute can be added simultaneously.
     */
    public function testAddingMultipleHashableAttributes()
    {
        // Add multiple attributes
        $this->model->addHashable('bar', 'baz');

        // Get the attributes
        $attributes = $this->model->getHashable();

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
     * Test that a single Hashable attribute can be removed.
     */
    public function testRemovingSingleHashableAttribute()
    {
        // Set the attributes
        $this->model->setHashable(['foo', 'bar']);

        // Remove a single attribute
        $this->model->removeHashable('bar');

        // Get the attributes
        $attributes = $this->model->getHashable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it did not return the unset value
        $this->assertContains('foo', $attributes);
        $this->assertNotContains('bar', $attributes);

        // Check that the count matches
        $this->assertCount(1, $attributes);
    }

    /**
     * Test that multiple Hashable attribute can be removed simultaneously.
     */
    public function testRemovingMultipleHashableAttributes()
    {
        // Set the attributes
        $this->model->setHashable(['foo', 'bar', 'baz']);

        // Remove multiple attributes
        $this->model->removeHashable('bar', 'baz');

        // Get the attributes
        $attributes = $this->model->getHashable();

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
     * Test that removing all Hashable attributes returns an empty array.
     */
    public function testRemovingAllHashableAttributes()
    {
        // Remove all attributes
        $this->model->removeHashable('foo');

        // Get the attributes
        $attributes = $this->model->getHashable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it did not returned the unset values
        $this->assertNotContains('foo', $attributes);

        // Check that the count matches
        $this->assertEmpty($attributes);
    }

    /**
     * Test that Hashable attributes can be merged.
     */
    public function testMergingHashableAttributes()
    {
        // Merge the attributes
        $this->model->mergeHashable(['bar']);

        // Get the attributes
        $attributes = $this->model->getHashable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it returned the merged values
        $this->assertContains('foo', $attributes);
        $this->assertContains('bar', $attributes);

        // Check that the count matches
        $this->assertCount(2, $attributes);
    }

    /**
     * Test that the Hasher can be gotten.
     */
    public function testGettingHasher()
    {
        // Check that its an Hasher
        $model = new ModelHashingStub();
        $hasher = $model->getHasher();
        $this->assertInstanceOf('\Illuminate\Hashing\BcryptHasher', $hasher, $hasher);
    }

    /**
     * Test that the Hasher can be set.
     */
    public function testSettingHasher()
    {
        // Set the Hasher
        $this->model->setHasher(new HasherStub());

        // Check that its an Hasher stub
        $this->assertInstanceOf('HasherStub', $this->model->getHasher());
    }

    /**
     * Test that isHashable returns true when hashing is enabled
     * and the attribute is Hashable.
     */
    public function testIsHashableReturnsTrue()
    {
        // Enable hashing
        $this->enableHashingOnMock();

        // Make sure getHashable is called and attribute is hashable
        $this->model->shouldReceive('getHashable')
            ->once()
            ->andReturn(['foo']);

        // Check that the attribute is hashable
        $this->assertTrue($this->model->isHashable('foo'));
    }

    /**
     * Test that non-Hashable attribute is not Hashable even
     * when Hashing is enabled.
     */
    public function testIsHashableReturnsFalseWhenNotSet()
    {
        // Enable hashing
        $this->enableHashingOnMock();

        // Make sure getHashable is called and attribute is not hashable
        $this->model->shouldReceive('getHashable')
            ->once()
            ->andReturn(['bar']);

        // Check that the attribute is not hashable
        $this->assertFalse($this->model->isHashable('foo'));
    }

    /**
     * Test that Hashable attribute is not Hashable when
     * Hashing is disabled.
     */
    public function testIsHashableReturnsFalseWhenDisabled()
    {
        // Disable hashing
        $this->disableHashingOnMock();

        // Make sure getHashable is called and attribute is hashable
        $this->model->shouldReceive('getHashable')
            ->andReturn(['foo']);

        // Check that the attribute is not hashable
        $this->assertFalse($this->model->isHashable('foo'));
    }

    /**
     * Test that isHashed returns true when attribute value is hashed.
     */
    public function testIsHashedReturnsTrue()
    {
        // Set the Hasher and hash a plain text value
        $this->setHasherOnMock();
        $hashed = $this->model->hash('plain text');

        // Set the attribute's hashed value
        $this->model->setRawAttributes(['foo' => $hashed]);

        // Check that the attribute is hashed
        $this->assertTrue($this->model->isHashed('foo'));
    }

    /**
     * Test that isHashed returns false when attribute value
     * is not hashed or otherwise doesn't exist.
     */
    public function testIsHashedReturnsFalse()
    {
        // Check that attribute is not hashed because it doesn't exist
        $this->assertFalse($this->model->isHashed('foo'));

        // Set an attribute's plain value
        $this->model->setRawAttributes(['foo' => 'plain text']);

        // Check that the attribute is not hashed
        $this->assertFalse($this->model->isHashed('foo'));
    }

    /**
     * Test that all hashable attributes are hashed.
     */
    public function testHashAttributes()
    {
        $hashables = ['foo', 'bar'];
        $this->model->setHashable($hashables);

        // Accessor should be used when getting the attribute value
        $this->model->shouldReceive('getAttribute')
            ->times(count($hashables));

        // Make sure the mutator is called for each attribute
        $this->model->shouldReceive('setHashingAttribute')
            ->times(count($hashables));

        // Do it
        $this->model->hashAttributes();
    }

    /**
     * Test that hash() hashes the value.
     */
    public function testHash()
    {
        $this->setHasherOnMock();
        $hashed = $this->model->hash('plain text');
        $this->assertNotEquals($hashed, 'plain text');
    }

    /**
     * Test that checkHash() compares a hash with the plain text.
     */
    public function testCheckHash()
    {
        $this->setHasherOnMock();
        $hashed = $this->model->hash('plain text');
        $this->assertTrue($this->model->checkHash('plain text', $hashed));
        $this->assertFalse($this->model->checkHash('plain text', 'foo'));
    }

    /**
     * Test that setHashingAttribute() hashes the attribute value.
     */
    public function testSetHashingAttribute()
    {
        $hashables = ['foo' => 'fighter', 'bar' => 'soap'];
        $this->setHasherOnMock();
        $this->model->unguard();
        $this->model->fill($hashables);
        $this->model->setHashable(array_keys($hashables));

        // Make sure each attribute is dirty before hashing
        $this->model->shouldReceive('isDirty')
            ->times(2)
            ->andReturn(true);

        // Do it
        $this->model->hashAttributes();

        // Check if getting the hashable attribute returns the hashed value
        $hashed = $this->model->getAttribute('foo');
        $this->assertNotEquals('plain text', $hashed);
        $this->assertEquals($hashed, $this->model->foo);

        // Check that the attribute is hashed
        $this->assertTrue($this->model->isHashed('foo'));
    }

    /**
     * Test that saveWithHashing calls hashAttributes even when disabled.
     */
    public function testSaveWithHashing()
    {
        // Disable hashing
        $this->model->setHashing(false);

        // Test that it is indeed enabled
        $this->model->shouldReceive('setHashing')
            ->once()
            ->with(true);

        // Mock save
        $this->model->shouldReceive('save')
            ->once()
            ->andReturn(true);

        // Test that it re-disabled
        $this->model->shouldReceive('setHashing')
            ->once()
            ->with(false);

        // Do it
        $response = $this->model->saveWithHashing();

        // Check that it returned true
        $this->assertTrue($response);

        // Check that hashing is still disabled
        $this->assertFalse($this->model->getHashing());
    }

    /**
     * Test that saveWithoutHashing does not call hashAttributes even when enabled.
     */
    public function testSaveWithOutHashing()
    {
        // Enable hashing
        $this->model->setHashing(true);

        // Test that it is indeed disabled
        $this->model->shouldReceive('setHashing')
            ->once()
            ->with(false);

        // Mock save
        $this->model->shouldReceive('save')
            ->once()
            ->andReturn(true);

        // Test that it re-enabled
        $this->model->shouldReceive('setHashing')
            ->once()
            ->with(true);

        // Do it
        $response = $this->model->saveWithOutHashing();

        // Check that it returned true
        $this->assertTrue($response);

        // Check that hashing is still enabled
        $this->assertTrue($this->model->getHashing());
    }

}

/**
 * Model Stub for Hashing Tests
 */
class ModelHashingStub extends Model
{
    /**
     * The attributes to hash before saving.
     *
     * @var array
     */
    protected $hashable = ['foo'];

    /**
     * Create a new model instance
     *
     * @return ModelHashingStub
     */
    public function __construct()
    {

        parent::__construct();

        // Assign a default hasher for mocking purposes
        $this->hasher = new BcryptHasher();
    }

}

/**
 * Hasher Stub for Hasher Tests
 */
class HasherStub extends BcryptHasher implements Hasher
{

}
