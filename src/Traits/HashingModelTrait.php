<?php namespace Esensi\Model\Traits;

use \Esensi\Model\Observers\HashingModelObserver;
use \Illuminate\Support\Facades\Hash;

/**
 * Trait that implements the HashingModelInterface
 *
 * @author daniel <daniel@bexarcreative.com>
 * @see \Esensi\Model\Contracts\HashingModelInterface
 */
trait HashingModelTrait {

    /**
     * Whether the model is hashing or not
     *
     * @var boolean
     */
    protected $hashing = true;

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
     * Get the hashable attributes
     *
     * @return array
     */
    public function getHashable()
    {
        return $this->hashable ?: [];
    }

    /**
     * Set the hashable attributes
     *
     * @param  array $attributes to hash
     * @return void
     */
    public function setHashable( array $attributes )
    {
        $this->hashable = $attributes;
    }

    /**
     * Returns whether or not the model will hash
     * attributes before saving
     *
     * @return boolean
     */
    public function getHashing()
    {
        return $this->hashing;
    }

    /**
     * Set whether or not the model will hash attributes
     * before saving
     *
     * @param  boolean
     * @return void
     */
    public function setHashing( $value )
    {
        $this->hashing = (bool) $value;
    }

    /**
     * Returns whether the attribute is hashable
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
     * Returns whether the attribute is hashed
     *
     * @param string $attribute name
     * @return boolean
     */
    public function isHashed( $attribute )
    {
        $info = password_get_info( $this->$attribute );
        return $info['algo'] !== 0;
    }

    /**
     * Hash attributes that should be hashed
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
     * Return a hashed string for the value
     *
     * @param string $value
     * @return string
     */
    public function hash( $value )
    {
        return Hash::make( $value );
    }

    /**
     * Return whether a plain value matches a hashed value
     *
     * @param string $value
     * @param string $hash to compare to
     * @return boolean
     */
    public function checkHash( $value, $hash )
    {
        return Hash::check( $value, $hash );
    }

    /**
     * Set a hashed value for a hashable attribute
     *
     * @param string $attribute name
     * @param string $value to hash
     * @return void
     */
    function setHashingAttribute( $attribute, $value )
    {
        // Set the attribute value like normal
        $this->$attribute = $value;

        // See if attribute needs hashing
        $needsHashing = $this->getAttribute( $attribute )
            && ( $this->isDirty( $attribute ) || ! $this->isHashed( $attribute ) );

        // Do the hashing if it needs it
        if ( $needsHashing )
        {
            $this->attributes[ $attribute ] = $this->hash( $this->getAttribute( $attribute ) );
        }
    }

}
