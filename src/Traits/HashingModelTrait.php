<?php

namespace Esensi\Model\Traits;

use Esensi\Model\Observers\HashingModelObserver;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Facades\Hash;

/**
 * Trait that implements the Hashing Model Interface
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2015 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 *
 * @see \Esensi\Model\Contracts\HashingModelInterface
 */
trait HashingModelTrait
{
    /**
     * Whether the model is hashing or not.
     *
     * @var boolean
     */
    protected $hashing = true;

    /**
     * The Hasher to use for hashing.
     *
     * @var \Illuminate\Contracts\Hashing\Hasher
     */
    protected $hasher;

    /**
     * Boot the trait's observers
     *
     * @return void
     */
    public static function bootHashingModelTrait()
    {
        static::observe(new HashingModelObserver);
    }

    /**
     * Get the hashable attributes.
     *
     * @return array
     */
    public function getHashable()
    {
        return $this->hashable ?: [];
    }

    /**
     * Set the hashable attributes.
     *
     * @param  array $attributes to hash
     * @return void
     */
    public function setHashable( array $attributes )
    {
        $this->hashable = $attributes;
    }

    /**
     * Add an attribute to the hashable array.
     *
     * @example addHashable( string $attribute, ... )
     * @param  string $attribute to hash
     * @return void
     */
    public function addHashable( $attribute )
    {
        $this->mergeHashable( func_get_args() );
    }

    /**
     * Remove an attribute from the hashable array.
     *
     * @example addHashable( string $attribute, ... )
     * @param  string $attribute to hash
     * @return void
     */
    public function removeHashable( $attribute )
    {
        $this->hashable = array_diff( $this->hashable, func_get_args() );
    }

    /**
     * Merge an array of attributes with the hashable array.
     *
     * @param  array $attributes to hash
     * @return void
     */
    public function mergeHashable( array $attributes )
    {
        $this->hashable = array_merge( $this->hashable, $attributes );
    }

    /**
     * Returns whether or not the model will hash
     * attributes before saving.
     *
     * @return boolean
     */
    public function getHashing()
    {
        return $this->hashing;
    }

    /**
     * Set whether or not the model will hash attributes
     * before saving.
     *
     * @param  boolean
     * @return void
     */
    public function setHashing( $value )
    {
        $this->hashing = (bool) $value;
    }

    /**
     * Set the Hasher to use for hashing.
     *
     * @return \Illuminate\Contracts\Hashing\Hasher
     */
    public function getHasher()
    {
        return $this->hasher ?: Hash::getFacadeRoot();
    }

    /**
     * Set the Hasher to use for hashing.
     *
     * @param \Illuminate\Contracts\Hashing\Hasher $hasher
     * @return void
     */
    public function setHasher( Hasher $hasher )
    {
        $this->hasher = $hasher;
    }

    /**
     * Returns whether the attribute is hashable.
     *
     * @param string $attribute name
     * @return boolean
     */
    public function isHashable( $attribute )
    {
        return $this->getHashing()
            && in_array( $attribute, $this->getHashable() );
    }

    /**
     * Returns whether the attribute is hashed.
     *
     * @param string $attribute name
     * @return boolean
     */
    public function isHashed( $attribute )
    {
        if( ! array_key_exists($attribute, $this->attributes) )
        {
            return false;
        }

        $info = password_get_info( $this->attributes[ $attribute ] );
        return (boolean) ( $info['algo'] != 0 );
    }

    /**
     * Hash attributes that should be hashed.
     *
     * @return void
     */
    public function hashAttributes()
    {
        foreach( $this->getHashable() as $attribute )
        {
            $this->setHashingAttribute( $attribute, $this->getAttribute($attribute) );
        }
    }

    /**
     * Return a hashed string for the value.
     *
     * @param string $value
     * @return string
     */
    public function hash( $value )
    {
        return $this->getHasher()
            ->make( $value );
    }

    /**
     * Return whether a plain value matches a hashed value.
     *
     * @param string $value
     * @param string $hash to compare to
     * @return boolean
     */
    public function checkHash( $value, $hash )
    {
        return $this->getHasher()
            ->check( $value, $hash );
    }

    /**
     * Set a hashed value for a hashable attribute.
     *
     * @param string $attribute name
     * @param string $value to hash
     * @return void
     */
    public function setHashingAttribute( $attribute, $value )
    {
        // Set the value which is presumably plain text
        $this->attributes[ $attribute ] = $value;

        // Do the hashing if it needs it
        if ( ! empty($value) && ($this->isDirty( $attribute ) || ! $this->isHashed( $attribute )) )
        {
            $this->attributes[ $attribute ] = $this->hash( $value );
        }
    }

    /**
     * Save with hashing even if hashing is disabled.
     *
     * @return boolean
     */
    public function saveWithHashing()
    {
        // Turn hashing on
        return $this->setHashingAndSave( true );
    }

    /**
     * Save without hashing even if hashing is enabled.
     *
     * @return boolean
     */
    public function saveWithoutHashing()
    {
        // Turn hashing off
        return $this->setHashingAndSave( false );
    }

    /**
     * Set hashing state and then save and then reset it.
     *
     * @param  boolean $hash
     * @return boolean
     */
    protected function setHashingAndSave( $hash )
    {
        // Set hashing state
        $hashing = $this->getHashing();
        $this->setHashing( $hash );

        // Save the model
        $result = $this->save();

        // Reset hashing back to it's previous state
        $this->setHashing( $hashing );

        return $result;
    }

}
