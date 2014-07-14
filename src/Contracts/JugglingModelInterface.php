<?php namespace Esensi\Model\Contracts;

/**
 * Juggling Model Interface
 *
 * @package Esensi\Model
 * @author Diego Caprioli <diego@emersonmedia.com>
 * @author Daniel LaBarge <dalabarge@emersonmedia.com>
 * @copyright 2014 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 */
interface JugglingModelInterface {

    /**
     * Get the juggable attributes.
     *
     * @return array
     */
    public function getJugglable();

    /**
     * Set the jugglable attributes.
     *
     * @param  array $attributes to juggle
     * @return void
     * @throws \InvalidArgumentException
     */
    public function setJugglable( array $attributes );

    /**
     * Add an attribute to the jugglable array.
     *
     * @param  string $attribute
     * @param  string $type
     * @return void
     */
    public function addJugglable( $attribute, $type );

    /**
     * Remove an attribute or several attributes from the jugglable array.
     *
     * @example removeJugglable( string $attribute, ... )
     * @param  mixed $attributes
     * @return void
     */
    public function removeJugglable( $attributes );

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
     * Gets the type that the attribute will be casted to.
     *
     * @param string $attribute
     * @return string
     */
    public function getJuggleType( $attribute );

    /**
     * Casts a value to the coresponding attribute type and sets
     * it on the attributes array of this model.
     *
     * @param string $attribute
     * @param string $value
     * @return void
     */
    public function juggleAttribute( $attribute, $value );

    /**
     * Juggles all attributes that are configured to be juggled.
     *
     * @return void
     */
    public function juggleAttributes();

    /**
     * Cast the value to the attribute's type as specified in the juggable array.
     *
     * @param  mixed  $value
     * @param  string $type
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function juggle( $value, $type );

    /**
     * Returns the value as a Carbon instance.
     *
     * @param  mixed $value
     * @return \Carbon\Carbon
     * @see \Illuminate\Database\Eloquent\Model::asDateTime()
     */
    public function juggleDate( $value );

    /**
     * Returns a string formated as ISO standard of 0000-00-00 00:00:00.
     *
     * @param  mixed $value
     * @return string
     */
    public function juggleDateTime( $value );

    /**
     * Returns the date as a Unix timestamp.
     *
     * @param  mixed $value
     * @return integer
     */
    public function juggleTimestamp( $value );

    /**
     * Returns the value as a boolean.
     *
     * @param  mixed $value
     * @return boolean
     */
    public function juggleBoolean( $value );

    /**
     * Returns the value as an integer.
     *
     * @param  mixed $value
     * @return integer
     */
    public function juggleInteger( $value );

    /**
     * Returns the value as a float.
     *
     * @param  mixed $value
     * @return float
     */
    public function juggleFloat( $value );

    /**
     * Returns the value as a string.
     *
     * @param  mixed $value
     * @return string
     */
    public function juggleString( $value );

    /**
     * Returns the value as an array.
     *
     * @param  mixed $value
     * @return array
     */
    public function juggleArray( $value );

}
