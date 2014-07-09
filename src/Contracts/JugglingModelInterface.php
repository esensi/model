<?php namespace Esensi\Model\Contracts;

/**
 * Juggling Model Interface
 *
 * @package Esensi\Model
 * @author Diego Caprioli <diego@emersonmedia.com>
 * @copyright 2014 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 */
interface JugglingModelInterface {


    /**
     * Get the juggable attributes
     *
     * @return array
     */
    function getJugglable();

    /**
     * Set the jugglable attributes.
     *
     * @param  array $attributes to purge
     * @return void
     */
    function setJugglable( array $attributes );

    /**
     * Add an attribute to the jugglable array.
     *
     * @example addJugglable( string $attribute, ... )
     * @param  string $attribute to purge
     * @return void
     */
    function addJugglable( $attribute );

    /**
     * Remove an attribute from the jugglable array.
     *
     * @example removeJugglable( string $attribute, ... )
     * @param  string $attribute to purge
     * @return void
     */
    function removeJugglable( $attribute );

    /**
     * Merge an array of attributes with the jugglable array.
     *
     * @param  array $attributes to purge
     * @return void
     */
    function mergeJugglable( array $attributes );

    /**
     * Returns whether or not the model will juggle attributes.
     *
     * @return boolean
     */
    function getJuggling();

    /**
     * Set whether or not the model will juggle attributes.
     *
     * @param  boolean
     * @return void
     */
    function setJuggling( $value );

    /**
     * Returns whether the attribute is purgeable.
     *
     * @param string $attribute name
     * @return boolean
     */
    function isJugglable( $attribute );

    /**
     * Casts a value to the coresponding attribute type and sets
     * it on the attributes array of this model
     *
     * @param  string $key
     * @param  string $value
     * @return  void
     */
    function juggleAttribute( $key, $value );

    /**
     * Juggles all attributes that are configured to be juggled.
     *
     * @return void
     */
    function juggleAttributes();

    /**
     * Cast the value to a the attribute's type as specified iun the juggable array
     *
     * @param  string $key
     * @param  mixed  $value
     * @return mixed
     */
    function juggle( $key, $value );

    /**
     * Gets the attribute juggled value.
     *
     * @param  string  $key
     * @return mixed
     */
    function getJuggledAttribute( $key, $value );

    /**
     * Sets the attribute value with the corresponding juggled value
     *
     * @param   string $key
     * @param   string $value
     * @return  void
     */
    function setJuggledAttribute( $key, $value );



}
