<?php

namespace Esensi\Model\Contracts;

use Illuminate\Encryption\Encrypter;

/**
 * Encrypting Model Interface
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2015 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
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
     * @param  array $attributes to encrypt
     * @return void
     */
    public function setEncryptable( array $attributes );

    /**
     * Add an attribute to the encryptable array.
     *
     * @example addEncryptable( string $attribute, ... )
     * @param  string $attribute to encrypt
     * @return void
     */
    public function addEncryptable( $attribute );

    /**
     * Remove an attribute from the encryptable array.
     *
     * @example addEncryptable( string $attribute, ... )
     * @param  string $attribute to encrypt
     * @return void
     */
    public function removeEncryptable( $attribute );

    /**
     * Merge an array of attributes with the encryptable array.
     *
     * @param  array $attributes to encrypt
     * @return void
     */
    public function mergeEncryptable( array $attributes );

    /**
     * Returns whether or not the model will encrypt
     * attributes when setting and decrypt when getting.
     *
     * @return boolean
     */
    public function getEncrypting();

    /**
     * Set whether or not the model will encrypt attributes
     * when setting and decrypt when getting.
     *
     * @param  boolean
     * @return void
     */
    public function setEncrypting( $value );

    /**
     * Set the Encrypter to use for encryption.
     *
     * @return \Illuminate\Encryption\Encrypter $encrypter
     */
    public function getEncrypter();

    /**
     * Set the Encrypter to use for encryption.
     *
     * @param \Illuminate\Encryption\Encrypter $encrypter
     * @return void
     */
    public function setEncrypter( Encrypter $encrypter );

    /**
     * Returns whether the attribute is encryptable.
     *
     * @param string $attribute name
     * @return boolean
     */
    public function isEncryptable( $attribute );

    /**
     * Returns whether the attribute is encrypted.
     *
     * @param string $attribute name
     * @return boolean
     */
    public function isEncrypted( $attribute );

    /**
     * Returns whether the attribute is decrypted.
     *
     * @param string $attribute name
     * @return boolean
     */
    public function isDecrypted( $attribute );

    /**
     * Encrypt attributes that should be encrypted.
     *
     * @return void
     */
    public function encryptAttributes();

    /**
     * Return an encrypted string for the value.
     *
     * @param string $value
     * @return string
     */
    public function encrypt( $value );

    /**
     * Return a decrypted string for the value.
     *
     * @param string $value
     * @return string
     */
    public function decrypt( $value );

}
