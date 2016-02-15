<?php

namespace Esensi\Model\Contracts;

use Illuminate\Contracts\Hashing\Hasher;

/**
 * Hashing Model Interface.
 *
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2015-2016 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/license.md MIT License
 *
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
     * @param array $attributes to hash
     */
    public function setHashable(array $attributes);

    /**
     * Add an attribute to the hashable array.
     *
     * @example addHashable( string $attribute, ... )
     *
     * @param string $attribute to purge
     */
    public function addHashable($attribute);

    /**
     * Remove an attribute from the hashable array.
     *
     * @example addHashable( string $attribute, ... )
     *
     * @param string $attribute to purge
     */
    public function removeHashable($attribute);

    /**
     * Merge an array of attributes with the hashable array.
     *
     * @param array $attributes to purge
     */
    public function mergeHashable(array $attributes);

    /**
     * Returns whether or not the model will hash
     * attributes before saving.
     *
     * @return bool
     */
    public function getHashing();

    /**
     * Set whether or not the model will hash attributes
     * before saving.
     *
     * @param  bool
     */
    public function setHashing($value);

    /**
     * Set the Hasher to use for hashing.
     *
     * @return Illuminate\Contracts\Hashing\Hasher
     */
    public function getHasher();

    /**
     * Set the Hasher to use for hashing.
     *
     * @param Illuminate\Contracts\Hashing\Hasher $hasher
     */
    public function setHasher(Hasher $hasher);

    /**
     * Returns whether the attribute is hashable.
     *
     * @param string $attribute name
     *
     * @return bool
     */
    public function isHashable($attribute);

    /**
     * Returns whether the attribute is hashed.
     *
     * @param string $attribute name
     *
     * @return bool
     */
    public function isHashed($attribute);

    /**
     * Hash attributes that should be hashed.
     */
    public function hashAttributes();

    /**
     * Return a hashed string for the value.
     *
     * @param string $value
     *
     * @return string
     */
    public function hash($value);

    /**
     * Return whether a plain value matches a hashed value.
     *
     * @param string $value
     * @param string $hash  to compare to
     *
     * @return bool
     */
    public function checkHash($value, $hash);

    /**
     * Save with hashing even if hashing is disabled.
     *
     * @return bool
     */
    public function saveWithHashing();

    /**
     * Save without hashing even if hashing is enabled.
     *
     * @return bool
     */
    public function saveWithoutHashing();
}
