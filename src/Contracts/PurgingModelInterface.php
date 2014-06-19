<?php namespace Esensi\Model\Contracts;

/**
 * Purging Model Interface
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <wishlist@emersonmedia.com>
 * @copyright 2014 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 */
interface PurgingModelInterface {

    /**
     * Get the purgeable attributes
     *
     * @return array
     */
    public function getPurgeable();

    /**
     * Set the purgeable attributes
     *
     * @param  array $attributes to encrypt
     * @return void
     */
    public function setPurgeable( array $attributes );

    /**
     * Returns whether or not the model will purge
     * attributes before saving
     *
     * @return boolean
     */
    public function getPurging();

    /**
     * Set whether or not the model will purge attributes
     * before saving
     *
     * @param  boolean
     * @return void
     */
    public function setPurging( $value );

    /**
     * Returns whether the attribute is purgeable
     *
     * @param string $attribute name
     * @return boolean
     */
    public function isPurgeable( $attribute );

    /**
     * Unset attributes that should be purged
     *
     * @return void
     */
    public function purgeAttributes();

}
