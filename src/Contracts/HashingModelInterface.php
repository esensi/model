<?php

namespace Esensi\Model\Contracts;

use Illuminate\Contracts\Hashing\Hasher;

/**
 * Hashing Model Interface
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2015 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 */
interface HashingModelInterface
{
    /**
     * Get the hashable attributes.
     *
     * @return array
     */
    public function getHashable();

    /**
     * Set the hashable attributes.
     *
     * @param  array $attributes to hash
     * @return void
     */
    public function setHashable( array $attributes );

    /**
     * Add an attribute to the hashable array.
     *
     * @example addHashable( string $attribute, ... )
     * @param  string $attribute to purge
     * @return void
     */
    public function addHashable( $attribute );

    /**
     * Remove an attribute from the hashable array.
     *
     * @example addHashable( string $attribute, ... )
     * @param  string $attribute to purge
     * @return void
     */
    public function removeHashable( $attribute );

    /**
     * Merge an array of attributes with the hashable array.
     *
     * @param  array $attributes to purge
     * @return void
     */
    public function mergeHashable( array $attributes );

    /**
     * Returns whether or not the model will hash
     * attributes before saving.
     *
     * @return boolean
     */
    public function getHashing();

    /**
     * Set whether or not the model will hash attributes
     * before saving.
     *
     * @param  boolean
     * @return void
     */
    public function setHashing( $value );

    /**
     * Set the Hasher to use for hashing.
     *
     * @return \Illuminate\Contracts\Hashing\Hasher
     */
    public function getHasher();

    /**
     * Set the Hasher to use for hashing.
     *
     * @param \Illuminate\Contracts\Hashing\Hasher $hasher
     * @return void
     */
    public function setHasher( Hasher $hasher );

    /**
     * Returns whether the attribute is hashable.
     *
     * @param string $attribute name
     * @return boolean
     */
    public function isHashable( $attribute );

    /**
     * Returns whether the attribute is hashed.
     *
     * @param string $attribute name
     * @return boolean
     */
    public function isHashed( $attribute );

    /**
     * Hash attributes that should be hashed.
     *
     * @return void
     */
    public function hashAttributes();

    /**
     * Return a hashed string for the value.
     *
     * @param string $value
     * @return string
     */
    public function hash( $value );

    /**
     * Return whether a plain value matches a hashed value.
     *
     * @param string $value
     * @param string $hash to compare to
     * @return boolean
     */
    public function checkHash( $value, $hash );

    /**
     * Save with hashing even if hashing is disabled.
     *
     * @return boolean
     */
    public function saveWithHashing();

    /**
     * Save without hashing even if hashing is enabled.
     *
     * @return boolean
     */
    public function saveWithoutHashing();

}
