<?php

namespace Esensi\Model\Contracts;

/**
 * Purging Model Interface.
 *
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2015-2016 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/license.md MIT License
 *
 * @link http://www.emersonmedia.com
 */
interface PurgingModelInterface
{
    /**
     * Get the purgeable attributes.
     *
     * @return array
     */
    public function getPurgeable();

    /**
     * Set the purgeable attributes.
     *
     * @param array $attributes to purge
     */
    public function setPurgeable(array $attributes);

    /**
     * Add an attribute to the purgeable array.
     *
     * @example addPurgeable( string $attribute, ... )
     *
     * @param string $attribute to purge
     */
    public function addPurgeable($attribute);

    /**
     * Remove an attribute from the purgeable array.
     *
     * @example removePurgeable( string $attribute, ... )
     *
     * @param string $attribute to purge
     */
    public function removePurgeable($attribute);

    /**
     * Merge an array of attributes with the purgeable array.
     *
     * @param array $attributes to purge
     */
    public function mergePurgeable(array $attributes);

    /**
     * Returns whether or not the model will purge
     * attributes before saving.
     *
     * @return bool
     */
    public function getPurging();

    /**
     * Set whether or not the model will purge attributes
     * before saving.
     *
     * @param  bool
     */
    public function setPurging($value);

    /**
     * Returns whether the attribute is purgeable.
     *
     * @param string $attribute name
     *
     * @return bool
     */
    public function isPurgeable($attribute);

    /**
     * Unset attributes that should be purged.
     */
    public function purgeAttributes();

    /**
     * Save with purging even if purging is disabled.
     *
     * @return bool
     */
    public function saveWithPurging();

    /**
     * Save without purging even if purging is enabled.
     *
     * @return bool
     */
    public function saveWithoutPurging();
}
