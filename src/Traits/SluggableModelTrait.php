<?php

namespace Esensi\Model\Traits;

/**
 * Trait that implements the Sluggable Model Interface.
 *
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2015-2016 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/license.md MIT License
 *
 * @link http://www.emersonmedia.com
 * @see Esensi\Model\Contracts\SluggableModelInterface
 */
trait SluggableModelTrait
{
    /**
     * Convert the value into a slug using a regexp replacement.
     *
     * @param string $value
     *
     * @return string
     */
    public function makeSlug($value)
    {
        $value = preg_replace('/[^a-zA-Z0-9\-\_]+/', '-', $value);

        return str_replace(['--', '__'], ['-', '_'], trim(strtolower($value), '-_'));
    }

    /**
     * Get the key that defines the model's sluggable attribute.
     *
     * @return string
     */
    public function getSlugKey()
    {
        return $this->sluggableKey ?: 'slug';
    }

    /**
     * Set the key that defines the model's sluggable attribute.
     *
     * @param string $key
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function setSlugKey($key)
    {
        $this->sluggableKey = $key;

        return $this;
    }

    /**
     * Get the value assigned to the sluggable key.
     *
     * @return string
     */
    public function getSlugAttribute()
    {
        return array_get($this->attributes, $this->getSlugKey());
    }

    /**
     * Mutate the value to a slug format and assign to the sluggable key.
     *
     * @param string $value
     */
    public function setSlugAttribute($value)
    {
        array_set($this->attributes, $this->getSlugKey(), $this->makeSlug($value));
    }
}
