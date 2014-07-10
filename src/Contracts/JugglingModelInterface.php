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
     * If juggling is active, it returns the juggleAttribute.
     * If not it just returns the value as if was passed
     *
     * @param  string $key
     * @param  mixed $value
     * @return mixed
     */
    public function getDynamicJuggle( $key, $value );

    /**
     * If juggling is active, it sets the attribute in the model
     * by type juggling the value first.
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function setDynamicJuggle( $key, $value );

    /**
     * Get the juggable attributes
     *
     * @return array
     */
    public function getJugglable();

    /**
     * Set the jugglable attributes.
     *
     * @param  array $attributes to juggle
     * @return void
     */
    public function setJugglable( array $attributes );

    /**
     * Add an attribute to the jugglable array.
     *
     * @example addJugglable( string $attribute, ... )
     * @param  string $attribute to juggle
     * @return void
     */
    public function addJugglable( $attribute );

    /**
     * Remove an attribute from the jugglable array.
     *
     * @example removeJugglable( string $attribute, ... )
     * @param  string $attribute to juggle
     * @return void
     */
    public function removeJugglable( $attribute );

    /**
     * Merge an array of attributes with the jugglable array.
     *
     * @param  array $attributes to juggle
     * @return void
     */
    public function mergeJugglable( array $attributes );

    /**
     * Returns whether or not the model will juggle attributes.
     *
     * @return boolean
     */
    public function getJuggling();

    /**
     * Set whether or not the model will juggle attributes.
     *
     * @param  boolean
     * @return void
     */
    public function setJuggling( $value );

    /**
     * Returns whether the attribute is type jugglable.
     *
     * @param string $attribute name
     * @return boolean
     */
    public function isJugglable( $attribute );

    /**
     * Casts a value to the coresponding attribute type and sets
     * it on the attributes array of this model.
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
     * Cast the value to the attribute's type as specified in the juggable array.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return mixed
     */
    function juggle( $type, $value );

    /**
     * Returns the value as a Carbon instance.
     *
     * @param  mixed $value
     * @return \Carbon\Carbon
     * @see \Illuminate\Database\Eloquent\Model::asDateTime()
     */
    function juggleDate( $value );

    /**
     * Returns a string formated as ISO standard for 0000-00-00 00:00:00.
     *
     * @param  mixed $value
     * @return string
     */
    function juggleDateTime( $value );

    /**
     * Returns the date as a Unix timestamp.
     *
     * @param  mixed $value
     * @return integer
     */
    function juggleTimestamp( $value );

    /**
     * Returns the value as boolean.
     *
     * @param  mixed $value
     * @return boolean
     */
    function juggleBoolean( $value );

    /**
     * Returns the value as integer.
     *
     * @param  mixed $value
     * @return integer
     */
    function juggleInteger( $value );

    /**
     * Returns the value as float.
     *
     * @param  mixed $value
     * @return float
     */
    function juggleFloat( $value );

    /**
     * Returns the value as string.
     *
     * @param  mixed $value
     * @return string
     */
    function juggleString( $value );

    /**
     * Returns the value as array.
     *
     * @param  mixed $value
     * @return array
     */
    function juggleArray( $value );

}
