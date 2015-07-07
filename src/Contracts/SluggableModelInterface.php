<?php

namespace Esensi\Model\Contracts;

/**
 * Sluggable Model Interface
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2015 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 */
interface SluggableModelInterface
{
    /**
     * Convert the value into a slug using a regexp replacement.
     *
     * @param  string $value
     * @return string
     */
    public function makeSlug($value);

    /**
     * Get the key that defines the model's sluggable attribute.
     *
     * @return string
     */
    public function getSlugKey();

    /**
     * Set the key that defines the model's sluggable attribute.
     *
     * @param string $key
     * @return Illuminate\Database\Eloquent\Model
     */
    public function setSlugKey($key);

    /**
     * Get the value assigned to the sluggable key.
     *
     * @return string
     */
    public function getSlugAttribute();

    /**
     * Mutate the value to a slug format and assign to the sluggable key.
     *
     * @param string $value
     * @return void
     */
    public function setSlugAttribute($value);

}
