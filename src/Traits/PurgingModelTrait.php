<?php namespace Esensi\Model\Traits;

use \Esensi\Model\Observers\PurgingModelObserver;
use \Illuminate\Support\Str;

/**
 * Trait that implements the PurgingModelInterface
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <wishlist@emersonmedia.com>
 * @copyright 2014 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 *
 * @see \Esensi\Model\Contracts\PurgingModelInterface
 */
trait PurgingModelTrait {

    /**
     * Whether the model is purging or not
     *
     * @var boolean
     */
    protected $purging = true;

    /**
     * Boot the trait's observers
     *
     * @return void
     */
    public static function bootPurgingModelTrait()
    {
        static::observe(new PurgingModelObserver);
    }

    /**
     * Get the purgeable attributes
     *
     * @return array
     */
    public function getPurgeable()
    {
        return $this->purgeable ?: [];
    }

    /**
     * Set the purgeable attributes
     *
     * @param  array $attributes to encrypt
     * @return void
     */
    public function setPurgeable( array $attributes )
    {
        $this->purgeable = $attributes;
    }

    /**
     * Returns whether or not the model will purge
     * attributes before saving
     *
     * @return boolean
     */
    public function getPurging()
    {
        return $this->purging;
    }

    /**
     * Set whether or not the model will purge attributes
     * before saving
     *
     * @param  boolean
     * @return void
     */
    public function setPurging( $value )
    {
        $this->purging = (bool) $value;
    }

    /**
     * Returns whether the attribute is purgeable
     *
     * @param string $attribute name
     * @return boolean
     */
    public function isPurgeable( $attribute )
    {
        return $this->getPurging()
            && in_array( $attribute, $this->getPurgeable() );
    }

    /**
     * Unset attributes that should be purged
     *
     * @return void
     */
    public function purgeAttributes()
    {
        // Get the attribute keys
        $keys = array_keys( $this->getAttributes() );

        // Filter out keys that should purged
        $attributes = array_filter( $keys,
            function( $key )
            {
                // Remove attributes that should be purged
                if ( in_array( $key, $this->getPurgeable() ) )
                {
                    return false;
                }

                // Remove attributes containing _confirmation suffix
                if ( Str::endsWith( $key, '_confirmation' ) )
                {
                    return false;
                }

                // Remove attributes containing _ prefix
                if ( Str::startsWith( $key, '_' ) )
                {
                    return false;
                }

                return true;
            });

        // Keep only the attributes that were not purged
        $this->attributes = array_intersect_key( $this->getAttributes(), array_flip( $attributes ) );
    }

}
