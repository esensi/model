<?php

namespace Esensi\Model\Traits;

use Esensi\Model\Observers\PurgingModelObserver;
use Illuminate\Support\Str;

/**
 * Trait that implements the Purging Model Interface.
 *
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2015-2016 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/license.md MIT License
 *
 * @link http://www.emersonmedia.com
 * @see Esensi\Model\Contracts\PurgingModelInterface
 */
trait PurgingModelTrait
{
    /**
     * Whether the model is purging or not.
     *
     * @var bool
     */
    protected $purging = true;

    /**
     * Boot the trait's observers.
     */
    public static function bootPurgingModelTrait()
    {
        static::observe(new PurgingModelObserver());
    }

    /**
     * Get the purgeable attributes.
     *
     * @return array
     */
    public function getPurgeable()
    {
        return $this->purgeable ?: [];
    }

    /**
     * Set the purgeable attributes.
     *
     * @param array $attributes to purge
     */
    public function setPurgeable(array $attributes)
    {
        $this->purgeable = $attributes;
    }

    /**
     * Add an attribute to the purgeable array.
     *
     * @example addPurgeable( string $attribute, ... )
     *
     * @param string $attribute to purge
     */
    public function addPurgeable($attribute)
    {
        $this->mergePurgeable(func_get_args());
    }

    /**
     * Remove an attribute from the purgeable array.
     *
     * @example removePurgeable( string $attribute, ... )
     *
     * @param string $attribute to purge
     */
    public function removePurgeable($attribute)
    {
        $this->purgeable = array_diff($this->purgeable, func_get_args());
    }

    /**
     * Merge an array of attributes with the purgeable array.
     *
     * @param array $attributes to purge
     */
    public function mergePurgeable(array $attributes)
    {
        $this->purgeable = array_merge($this->purgeable, $attributes);
    }

    /**
     * Returns whether or not the model will purge
     * attributes before saving.
     *
     * @return bool
     */
    public function getPurging()
    {
        return $this->purging;
    }

    /**
     * Set whether or not the model will purge attributes
     * before saving.
     *
     * @param  bool
     */
    public function setPurging($value)
    {
        $this->purging = (bool) $value;
    }

    /**
     * Returns whether the attribute is purgeable.
     *
     * @param string $attribute name
     *
     * @return bool
     */
    public function isPurgeable($attribute)
    {
        return $this->getPurging()
            && in_array($attribute, $this->getPurgeable());
    }

    /**
     * Unset attributes that should be purged.
     */
    public function purgeAttributes()
    {
        // Get the attribute keys
        $keys = array_keys($this->getAttributes());

        // Filter out keys that should purged
        $attributes = array_filter($keys, function ($key) {
            // Remove attributes that should be purged
            if (in_array($key, $this->getPurgeable())) {
                return false;
            }

            // Remove attributes ending with _confirmation
            if (Str::endsWith($key, '_confirmation')) {
                return false;
            }

            // Remove attributes starting with _ prefix
            if (Str::startsWith($key, '_')) {
                return false;
            }

            return true;
        });

        // Keep only the attributes that were not purged
        $this->attributes = array_intersect_key($this->getAttributes(), array_flip($attributes));
    }

    /**
     * Save with purging even if purging is disabled.
     *
     * @return bool
     */
    public function saveWithPurging()
    {
        // Turn purging on
        return $this->setPurgingAndSave(true);
    }

    /**
     * Save without purging even if purging is enabled.
     *
     * @return bool
     */
    public function saveWithoutPurging()
    {
        // Turn purging off
        return $this->setPurgingAndSave(false);
    }

    /**
     * Set purging state and then save and then reset it.
     *
     * @param bool $purge
     *
     * @return bool
     */
    protected function setPurgingAndSave($purge)
    {
        // Set purging state
        $purging = $this->getPurging();
        $this->setPurging($purge);

        // Save the model
        $result = $this->save();

        // Reset purging back to it's previous state
        $this->setPurging($purging);

        return $result;
    }
}
