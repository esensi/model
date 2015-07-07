<?php

use Esensi\Model\Model;
use Illuminate\Encryption\Encrypter;
use PHPUnit_Framework_TestCase as PHPUnit;

/**
 * Tests for the Encrypting Model Trait
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2014 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 */
class EncryptingModelTraitTest extends PHPUnit
{
    /**
     * Set Up and Prepare Tests.
     */
    public function setUp()
    {
        // Mock the Model that uses the custom trait
        $this->model = Mockery::mock('ModelEncryptingStub');
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
     * Enables encrypting on mock.
     */
    protected function enableEncryptingOnMock()
    {
        $this->model->shouldReceive('getEncrypting')
            ->atleast()
            ->times(1)
            ->andReturn(true);
    }

    /**
     * Disables encrypting on mock.
     */
    protected function disableEncryptingOnMock()
    {
        $this->model->shouldReceive('getEncrypting')
            ->atleast()
            ->times(1)
            ->andReturn(false);
    }

    /**
     * Sets the encrypter on mock.
     */
    public function setEncrypterOnMock()
    {
        $encrypter = EncrypterStub::newInstance();
        $this->model->setEncrypter($encrypter);
    }

    /**
     * Test that Encrypting is enabled by default.
     */
    public function testEncryptingEnabledByDefault()
    {
        $this->assertTrue($this->model->getEncrypting());
    }

    /**
     * Test that Encrypting can be enabled and disabled.
     */
    public function testSettingEncryption()
    {
        // Disable Encrypting
        $this->model->setEncrypting(false);
        $this->assertFalse($this->model->getEncrypting());

        // Enable Encrypting
        $this->model->setEncrypting(true);
        $this->assertTrue($this->model->getEncrypting());
    }

    /**
     * Test that Encryptable attributes can be gotten.
     */
    public function testGettingEncryptableAttributes()
    {
        // Get the attributes
        $attributes = $this->model->getEncryptable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it returned the default value
        $this->assertContains('foo', $attributes);

        // Check that the count matches
        $this->assertCount(1, $attributes);
    }

    /**
     * Test that Encryptable attributes can be set.
     */
    public function testSettingEncryptableAttributes()
    {
        // Set the attributes
        $this->model->setEncryptable(['bar']);

        // Get the attributes
        $attributes = $this->model->getEncryptable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it returned the set value
        $this->assertContains('bar', $attributes);

        // Check that the count matches
        $this->assertCount(1, $attributes);
    }

    /**
     * Test that a single Encryptable attribute can be added.
     */
    public function testAddingSingleEncryptableAttribute()
    {
        // Add a single attribute
        $this->model->addEncryptable('bar');

        // Get the attributes
        $attributes = $this->model->getEncryptable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it returned the set value
        $this->assertContains('foo', $attributes);
        $this->assertContains('bar', $attributes);

        // Check that the count matches
        $this->assertCount(2, $attributes);
    }

    /**
     * Test that multiple Encryptable attribute can be added simultaneously.
     */
    public function testAddingMultipleEncryptableAttributes()
    {
        // Add multiple attributes
        $this->model->addEncryptable('bar', 'baz');

        // Get the attributes
        $attributes = $this->model->getEncryptable();

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
     * Test that a single Encryptable attribute can be removed.
     */
    public function testRemovingSingleEncryptableAttribute()
    {
        // Set the attributes
        $this->model->setEncryptable(['foo', 'bar']);

        // Remove a single attribute
        $this->model->removeEncryptable('bar');

        // Get the attributes
        $attributes = $this->model->getEncryptable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it did not return the unset value
        $this->assertContains('foo', $attributes);
        $this->assertNotContains('bar', $attributes);

        // Check that the count matches
        $this->assertCount(1, $attributes);
    }

    /**
     * Test that multiple Encryptable attribute can be removed simultaneously.
     */
    public function testRemovingMultipleEncryptableAttributes()
    {
        // Set the attributes
        $this->model->setEncryptable(['foo', 'bar', 'baz']);

        // Remove multiple attributes
        $this->model->removeEncryptable('bar', 'baz');

        // Get the attributes
        $attributes = $this->model->getEncryptable();

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
     * Test that removing all Encryptable attributes returns an empty array.
     */
    public function testRemovingAllEncryptableAttributes()
    {
        // Remove all attributes
        $this->model->removeEncryptable('foo');

        // Get the attributes
        $attributes = $this->model->getEncryptable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it did not returned the unset values
        $this->assertNotContains('foo', $attributes);

        // Check that the count matches
        $this->assertEmpty($attributes);
    }

    /**
     * Test that Encryptable attributes can be merged.
     */
    public function testMergingEncryptableAttributes()
    {
        // Merge the attributes
        $this->model->mergeEncryptable(['bar']);

        // Get the attributes
        $attributes = $this->model->getEncryptable();

        // Check that its an array
        $this->assertTrue(is_array($attributes));

        // Check that it returned the merged values
        $this->assertContains('foo', $attributes);
        $this->assertContains('bar', $attributes);

        // Check that the count matches
        $this->assertCount(2, $attributes);
    }

    /**
     * Test that the Encrypter can be gotten.
     */
    public function testGettingEncrypter()
    {
        // Check that its an Encrypter
        $model = new ModelEncryptingStub();
        $encrypter = $model->getEncrypter();
        $this->assertInstanceOf('\Illuminate\Encryption\Encrypter', $encrypter, $encrypter);
    }

    /**
     * Test that the Encrypter can be set.
     */
    public function testSettingEncrypter()
    {
        // Set the Encrypter
        $encrypter = EncrypterStub::newInstance();
        $this->model->setEncrypter($encrypter);

        // Check that its an Encrypter stub
        $this->assertInstanceOf('EncrypterStub', $this->model->getEncrypter());
    }

    /**
     * Test that isEncryptable returns true when encryption is enabled
     * and the attribute is Encryptable.
     */
    public function testIsEncryptableReturnsTrue()
    {
        // Enable encryption
        $this->enableEncryptingOnMock();

        // Make sure getEncryptable is called and attribute is encryptable
        $this->model->shouldReceive('getEncryptable')
            ->once()
            ->andReturn(['foo']);

        // Check that the attribute is encryptable
        $this->assertTrue($this->model->isEncryptable('foo'));
    }

    /**
     * Test that non-Encryptable attribute is not Encryptable even
     * when encrypting is enabled.
     */
    public function testIsEncryptableReturnsFalseWhenNotSet()
    {
        // Enable encryption
        $this->enableEncryptingOnMock();

        // Make sure getEncryptable is called and attribute is not encryptable
        $this->model->shouldReceive('getEncryptable')
            ->once()
            ->andReturn(['bar']);

        // Check that the attribute is not encryptable
        $this->assertFalse($this->model->isEncryptable('foo'));
    }

    /**
     * Test that Encryptable attribute is not Encryptable when
     * encrypting is disabled.
     */
    public function testIsEncryptableReturnsFalseWhenDisabled()
    {
        // Disable encryption
        $this->disableEncryptingOnMock();

        // Make sure getEncryptable is called and attribute is encryptable
        $this->model->shouldReceive('getEncryptable')
            ->andReturn(['foo']);

        // Check that the attribute is not encryptable
        $this->assertFalse($this->model->isEncryptable('foo'));
    }

    /**
     * Test that isEncrypted returns true when attribute value is encrypted.
     */
    public function testIsEncryptedReturnsTrue()
    {
        // Set the Encrypter and encrypt a plain text value
        $this->setEncrypterOnMock();
        $encrypted = $this->model->encrypt('plain text');

        // Set the attribute's encrypted value
        $this->model->setRawAttributes(['foo' => $encrypted]);

        // Decrypt should return the decrypted value
        $this->model->shouldReceive('decrypt')
            ->once()
            ->with($encrypted)
            ->andReturn('plain text');

        // Check that the attribute is encrypted
        $this->assertTrue($this->model->isEncrypted('foo'));
    }

    /**
     * Test that isEncrypted returns false when attribute value
     * is not encrypted or otherwise doesn't exist.
     */
    public function testIsEncryptedReturnsFalse()
    {
        // Check that attribute is not encrypted because it doesn't exist
        $this->assertFalse($this->model->isEncrypted('foo'));

        // Set an attribute's plain value
        $this->model->setRawAttributes(['foo' => 'plain text']);

        // Decrypt should throw an exception
        $this->model->shouldReceive('decrypt')
            ->once()
            ->with('plain text')
            ->andThrow('\Illuminate\Contracts\Encryption\DecryptException');

        // Check that the attribute is not encrypted
        $this->assertFalse($this->model->isEncrypted('foo'));
    }

    /**
     * Test that isDecrypted returns the inverse of isEncrypted.
     */
    public function testIsDecryptedReturnsTheInverse()
    {
        $this->assertFalse($this->model->isEncrypted('foo'));
        $this->assertTrue($this->model->isDecrypted('foo'));
    }

    /**
     * Test that all encryptable attributes are encrypted.
     */
    public function testEncryptAttributes()
    {
        $encryptables = ['foo' => 'fighter', 'bar' => 'soap'];
        $this->model->setRawAttributes($encryptables);
        $this->model->setEncryptable(array_keys($encryptables));

        // Mutators should be called to get the attribute
        $this->model->shouldReceive('setEncryptingAttribute')
            ->times(count($encryptables));

        // Do it
        $this->model->encryptAttributes();
    }

    /**
     * Test that encrypt() encrypts the value.
     */
    public function testEncrypt()
    {
        $this->setEncrypterOnMock();
        $encrypted = $this->model->encrypt('plain text');
        $this->assertNotEquals($encrypted, 'plain text');
    }

    /**
     * Test that decrypt() decrypts the value.
     */
    public function testDecrypt()
    {
        $this->setEncrypterOnMock();
        $encrypted = $this->model->encrypt('plain text');

        // Check if the decrypted value matches the plain text
        $decrypted = $this->model->decrypt($encrypted);
        $this->assertEquals('plain text', $decrypted);
    }

    /**
     * Test that getEncryptedAttribute() decrypts the attribute value.
     */
    public function testGetDecryptAttribute()
    {
        // Set the encryptable attribute value as encrypted
        $this->setEncrypterOnMock();
        $encrypted = $this->model->encrypt('plain text');
        $this->model->setRawAttributes(['foo' => $encrypted]);

        // Accessor should be called
        $this->model->shouldReceive('getEncryptedAttribute')
            ->once()
            ->with('foo')
            ->andReturn('plain text');

        // Decrypt should be called
        $this->model->shouldReceive('decrypt')
            ->times(2)
            ->with($encrypted)
            ->andReturn('plain text');

        // Check if getting the encryptable attribute returns the decrypted value
        $decrypted = $this->model->foo;
        $this->assertEquals('plain text', $decrypted);
        $this->assertNotEquals('plain text', $this->model->getAttribute('foo'));

        // Check that the attribute is still encrypted
        $this->assertFalse($this->model->isDecrypted('foo'));
    }

    /**
     * Test that setEncryptingAttribute() encrypts the attribute value.
     */
    public function testSetEncryptingAttribute()
    {
        $this->setEncrypterOnMock();

        // Attribute should be encryptable
        $this->model->shouldReceive('isEncryptable')
            ->times(2)
            ->with('foo')
            ->andReturn(true);

        // Attribute should be already be plain text
        $this->model->shouldReceive('isDecrypted')
            ->once()
            ->with('foo')
            ->andReturn(true);

        // Set the encryptable attribute value
        $this->model->foo = 'plain text';

        // Check if getting the encryptable attribute returns the encrypted value
        $encrypted = $this->model->getAttribute('foo');
        $this->assertNotEquals('plain text', $encrypted);
        $this->assertEquals('plain text', $this->model->foo);

        // Check that the attribute is encrypted
        $this->assertTrue($this->model->isEncrypted('foo'));
    }

}

/**
 * Model Stub for Encrypting Tests
 */
class ModelEncryptingStub extends Model
{
    /**
     * The attributes to encrypt when set and
     * decrypt when gotten.
     *
     * @var array
     */
    protected $encryptable = ['foo'];

    /**
     * Create a new model instance.
     *
     * @return ModelEncryptingStub
     */
    public function __construct(){

        parent::__construct();

        // Assign a default encrypter for mocking purposes
        $this->encrypter = EncrypterStub::newInstance();
    }

}

/**
 * Encrypter Stub for Encrypter Tests
 */
class EncrypterStub extends Encrypter
{
    /**
     * Construct new instance of EncrypterStub
     *
     * @return Illuminate\Encryption\Encrypter
     */
    public static function newInstance(){
        return new EncrypterStub('TESTL3drFL2P3Rnjw216bOZxkgZ0tKEY', 'AES-256-CBC');
    }
}
