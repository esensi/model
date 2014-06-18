<?php namespace Esensi\Model\Contracts;

/**
 * Hashing Model Interface
 *
 * @author daniel <daniel@bexarcreative.com>
 */
interface HashingModelInterface {

    /**
     * Get the hashable attributes
     *
     * @return array
     */
    public function getHashable();

    /**
     * Set the hashable attributes
     *
     * @param  array $attributes to hash
     * @return void
     */
    public function setHashable( array $attributes );

    /**
     * Returns whether or not the model will hash
     * attributes before saving
     *
     * @return boolean
     */
    public function getHashing();

    /**
     * Set whether or not the model will hash attributes
     * before saving
     *
     * @param  boolean
     * @return void
     */
    public function setHashing( $value );

    /**
     * Returns whether the attribute is hashable
     *
     * @param string $attribute name
     * @return boolean
     */
    public function isHashable( $attribute );

    /**
     * Returns whether the attribute is hashed
     *
     * @param string $attribute name
     * @return boolean
     */
    public function isHashed( $attribute );

    /**
     * Hash attributes that should be hashed
     *
     * @return void
     */
    public function hashAttributes();

    /**
     * Return a hashed string for the value
     *
     * @param string $value
     * @return string
     */
    public function hash( $value );

    /**
     * Return whether a plain value matches a hashed value
     *
     * @param string $value
     * @param string $hash to compare to
     * @return boolean
     */
    public function checkHash( $value, $hash );

    /**
     * Set a hashed value for a hashable attribute
     *
     * @param string $attribute name
     * @param string $value to hash
     * @return void
     */
    function setHashingAttribute( $attribute, $value );

}
