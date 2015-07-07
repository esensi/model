<?php

namespace Esensi\Model\Contracts;

/**
 * Purging Model Interface
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2015 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
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
     * @param  array $attributes to purge
     * @return void
     */
    public function setPurgeable( array $attributes );

    /**
     * Add an attribute to the purgeable array.
     *
     * @example addPurgeable( string $attribute, ... )
     * @param  string $attribute to purge
     * @return void
     */
    public function addPurgeable( $attribute );

    /**
     * Remove an attribute from the purgeable array.
     *
     * @example removePurgeable( string $attribute, ... )
     * @param  string $attribute to purge
     * @return void
     */
    public function removePurgeable( $attribute );

    /**
     * Merge an array of attributes with the purgeable array.
     *
     * @param  array $attributes to purge
     * @return void
     */
    public function mergePurgeable( array $attributes );

    /**
     * Returns whether or not the model will purge
     * attributes before saving.
     *
     * @return boolean
     */
    public function getPurging();

    /**
     * Set whether or not the model will purge attributes
     * before saving.
     *
     * @param  boolean
     * @return void
     */
    public function setPurging( $value );

    /**
     * Returns whether the attribute is purgeable.
     *
     * @param string $attribute name
     * @return boolean
     */
    public function isPurgeable( $attribute );

    /**
     * Unset attributes that should be purged.
     *
     * @return void
     */
    public function purgeAttributes();

    /**
     * Save with purging even if purging is disabled.
     *
     * @return boolean
     */
    public function saveWithPurging();

    /**
     * Save without purging even if purging is enabled.
     *
     * @return boolean
     */
    public function saveWithoutPurging();

}
