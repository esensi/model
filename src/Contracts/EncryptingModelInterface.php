<?php

namespace Esensi\Model\Contracts;

use Illuminate\Encryption\Encrypter;

/**
 * Encrypting Model Interface.
 *
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2015-2016 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/license.md MIT License
 *
 * @link http://www.emersonmedia.com
 */
interface EncryptingModelInterface
{
    /**
     * Get the encryptable attributes.
     *
     * @return array
     */
    public function getEncryptable();

    /**
     * Set the encryptable attributes.
     *
     * @param array $attributes to encrypt
     */
    public function setEncryptable(array $attributes);

    /**
     * Add an attribute to the encryptable array.
     *
     * @example addEncryptable( string $attribute, ... )
     *
     * @param string $attribute to encrypt
     */
    public function addEncryptable($attribute);

    /**
     * Remove an attribute from the encryptable array.
     *
     * @example addEncryptable( string $attribute, ... )
     *
     * @param string $attribute to encrypt
     */
    public function removeEncryptable($attribute);

    /**
     * Merge an array of attributes with the encryptable array.
     *
     * @param array $attributes to encrypt
     */
    public function mergeEncryptable(array $attributes);

    /**
     * Returns whether or not the model will encrypt
     * attributes when setting and decrypt when getting.
     *
     * @return bool
     */
    public function getEncrypting();

    /**
     * Set whether or not the model will encrypt attributes
     * when setting and decrypt when getting.
     *
     * @param  bool
     */
    public function setEncrypting($value);

    /**
     * Set the Encrypter to use for encryption.
     *
     * @return Illuminate\Encryption\Encrypter $encrypter
     */
    public function getEncrypter();

    /**
     * Set the Encrypter to use for encryption.
     *
     * @param Illuminate\Encryption\Encrypter $encrypter
     */
    public function setEncrypter(Encrypter $encrypter);

    /**
     * Returns whether the attribute is encryptable.
     *
     * @param string $attribute name
     *
     * @return bool
     */
    public function isEncryptable($attribute);

    /**
     * Returns whether the attribute is encrypted.
     *
     * @param string $attribute name
     *
     * @return bool
     */
    public function isEncrypted($attribute);

    /**
     * Returns whether the attribute is decrypted.
     *
     * @param string $attribute name
     *
     * @return bool
     */
    public function isDecrypted($attribute);

    /**
     * Encrypt attributes that should be encrypted.
     */
    public function encryptAttributes();

    /**
     * Return an encrypted string for the value.
     *
     * @param string $value
     *
     * @return string
     */
    public function encrypt($value);

    /**
     * Return a decrypted string for the value.
     *
     * @param string $value
     *
     * @return string
     */
    public function decrypt($value);
}
