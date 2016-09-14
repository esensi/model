<?php

namespace Esensi\Model\Traits;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Crypt;

/**
 * Trait that implements the Encrypting Model Interface.
 *
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2015-2016 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/license.md MIT License
 *
 * @link http://www.emersonmedia.com
 * @see Esensi\Model\Contracts\EncryptionModelInterface
 */
trait EncryptingModelTrait
{
    /**
     * Whether the model is encrypting or not.
     *
     * @var bool
     */
    protected $encrypting = true;

    /**
     * The Encrypter to use for encryption.
     *
     * @var Illuminate\Encryption\Encrypter
     */
    protected $encrypter;

    /**
     * Dynamically retrieve attributes.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        // Dynamically retrieve the encrypted attribute
        $value = $this->getDynamicEncrypted($key);
        if ( ! is_null($value) || is_string($value)) {
            return $value;
        }

        // Default Eloquent dynamic getter
        return parent::__get($key);
    }

    /**
     * Dynamically set attributes.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function __set($key, $value)
    {
        // Dynamically set the encryptable attribute
        if ( ! empty($value) && $this->setDynamicEncryptable($key, $value)) {
            return;
        }

        // Default Eloquent dynamic setter
        return parent::__set($key, $value);
    }

    /**
     * Get an encrypted attribute dynamically.
     *
     * @param string $attribute
     *
     * @return mixed
     */
    protected function getDynamicEncrypted($attribute)
    {
        // Dynamically get the encrypted attributes
        if ($this->isEncryptable($attribute)) {
            // Decrypt only encrypted values
            if ($this->isEncrypted($attribute)) {
                return $this->getEncryptedAttribute($attribute);
            }
        }
    }

    /**
     * Set an encryptable attribute dynamically.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    protected function setDynamicEncryptable($attribute, $value)
    {
        // Dynamically set the encryptable attribute
        if ($this->isEncryptable($attribute)) {
            // Encrypt only decrypted values
            if ($this->isDecrypted($attribute)) {
                $this->setEncryptingAttribute($attribute, $value);

                return true;
            }
        }

        return false;
    }

    /**
     * Get the encryptable attributes.
     *
     * @return array
     */
    public function getEncryptable()
    {
        return $this->encryptable ?: [];
    }

    /**
     * Set the encryptable attributes.
     *
     * @param array $attributes to encrypt
     */
    public function setEncryptable(array $attributes)
    {
        $this->encryptable = $attributes;
    }

    /**
     * Add an attribute to the encryptable array.
     *
     * @example addEncryptable( string $attribute, ... )
     *
     * @param string $attribute to purge
     */
    public function addEncryptable($attribute)
    {
        $this->mergeEncryptable(func_get_args());
    }

    /**
     * Remove an attribute from the encryptable array.
     *
     * @example addEncryptable( string $attribute, ... )
     *
     * @param string $attribute to purge
     */
    public function removeEncryptable($attribute)
    {
        $this->encryptable = array_diff($this->encryptable, func_get_args());
    }

    /**
     * Merge an array of attributes with the encryptable array.
     *
     * @param array $attributes to purge
     */
    public function mergeEncryptable(array $attributes)
    {
        $this->encryptable = array_merge($this->encryptable, $attributes);
    }

    /**
     * Returns whether or not the model will encrypt
     * attributes when setting and decrypt when getting.
     *
     * @return bool
     */
    public function getEncrypting()
    {
        return $this->encrypting;
    }

    /**
     * Set whether or not the model will encrypt attributes
     * when setting and decrypt when getting.
     *
     * @param  bool
     */
    public function setEncrypting($value)
    {
        $this->encrypting = (bool) $value;
    }

    /**
     * Set the Encrypter to use for encryption.
     *
     * @return Illuminate\Encryption\Encrypter
     */
    public function getEncrypter()
    {
        return $this->encrypter ?: Crypt::getFacadeRoot();
    }

    /**
     * Set the Encrypter to use for encryption.
     *
     * @param Illuminate\Encryption\Encrypter $encrypter
     */
    public function setEncrypter(Encrypter $encrypter)
    {
        $this->encrypter = $encrypter;
    }

    /**
     * Returns whether the attribute is encryptable.
     *
     * @param string $attribute name
     *
     * @return bool
     */
    public function isEncryptable($attribute)
    {
        return $this->getEncrypting()
            && in_array($attribute, $this->getEncryptable());
    }

    /**
     * Returns whether the attribute is encrypted.
     *
     * @param string $attribute name
     *
     * @return bool
     */
    public function isEncrypted($attribute)
    {
        if ( ! array_key_exists($attribute, $this->attributes)) {
            return false;
        }

        try {
            $this->decrypt(array_get($this->attributes, $attribute));
        } catch (DecryptException $exception) {
            return false;
        }

        return true;
    }

    /**
     * Returns whether the attribute is decrypted.
     *
     * @param string $attribute name
     *
     * @return bool
     */
    public function isDecrypted($attribute)
    {
        return ! $this->isEncrypted($attribute);
    }

    /**
     * Encrypt attributes that should be encrypted.
     */
    public function encryptAttributes()
    {
        foreach ($this->getEncryptable() as $attribute) {
            $this->setEncryptingAttribute($attribute, array_get($this->attributes, $attribute));
        }
    }

    /**
     * Return an encrypted string for the value.
     *
     * @param string $value
     *
     * @return string
     */
    public function encrypt($value)
    {
        return $this->getEncrypter()
            ->encrypt($value);
    }

    /**
     * Return a decrypted string for the value.
     *
     * @param string $value
     *
     * @return string
     */
    public function decrypt($value)
    {
        return $this->getEncrypter()
            ->decrypt($value);
    }

    /**
     * Get the decrypted value for an encrypted attribute.
     *
     * @param string $attribute name
     *
     * @return string
     */
    public function getEncryptedAttribute($attribute)
    {
        $value = array_get($this->attributes, $attribute);

        return $this->isEncrypted($attribute) ? $this->decrypt($value) : $value;
    }

    /**
     * Set an encrypted value for an encryptable attribute.
     *
     * @param string $attribute name
     * @param string $value     to encrypt
     */
    public function setEncryptingAttribute($attribute, $value)
    {
        array_set($this->attributes, $attribute, $this->encrypt($value));
    }
}
