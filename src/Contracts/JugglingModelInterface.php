<?php

namespace Esensi\Model\Contracts;

/**
 * Juggling Model Interface.
 *
 * @author Diego Caprioli <diego@emersonmedia.com>
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2015-2016 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/license.md MIT License
 *
 * @link http://www.emersonmedia.com
 */
interface JugglingModelInterface
{
    /**
     * Get the juggable attributes.
     *
     * @return array
     */
    public function getJugglable();

    /**
     * Set the jugglable attributes.
     *
     * @param array $attributes to juggle
     *
     * @throws InvalidArgumentException
     */
    public function setJugglable(array $attributes);

    /**
     * Add an attribute to the jugglable array.
     *
     * @param string $attribute
     * @param string $type
     *
     * @throws InvalidArgumentException
     */
    public function addJugglable($attribute, $type);

    /**
     * Remove an attribute or several attributes from the jugglable array.
     *
     * @example removeJugglable( string $attribute, ... )
     *
     * @param mixed $attributes
     */
    public function removeJugglable($attributes);

    /**
     * Merge an array of attributes with the jugglable array.
     *
     * @param array $attributes to juggle
     *
     * @throws InvalidArgumentException
     */
    public function mergeJugglable(array $attributes);

    /**
     * Returns whether or not the model will juggle attributes.
     *
     * @return bool
     */
    public function getJuggling();

    /**
     * Set whether or not the model will juggle attributes.
     *
     * @param  bool
     */
    public function setJuggling($value);

    /**
     * Returns whether the attribute is type jugglable.
     *
     * @param string $attribute name
     *
     * @return bool
     */
    public function isJugglable($attribute);

    /**
     * Returns whether the type is a type that can be juggled to.
     *
     * @param string $type to cast
     *
     * @return bool
     */
    public function isJuggleType($type);

    /**
     * Checks whether the type is a type that can be juggled to.
     *
     * @param string $type to cast
     *
     * @throws InvalidArgumentException
     *
     * @return bool
     */
    public function checkJuggleType($type);

    /**
     * Build the method name that the type normalizes to.
     *
     * @param string $type to cast
     *
     * @return string
     */
    public function buildJuggleMethod($type);

    /**
     * Gets the type that the attribute will be casted to.
     *
     * @param string $attribute
     *
     * @return string
     */
    public function getJuggleType($attribute);

    /**
     * Juggles all attributes that are configured to be juggled.
     */
    public function juggleAttributes();

    /**
     * Casts a value to the coresponding attribute type and sets
     * it on the attributes array of this model.
     *
     * @param string $attribute
     * @param string $value
     */
    public function juggleAttribute($attribute, $value);

    /**
     * Cast the value to the attribute's type as specified in the juggable array.
     *
     * @param mixed  $value
     * @param string $type
     *
     * @throws InvalidArgumentException
     *
     * @return mixed
     */
    public function juggle($value, $type);

    /**
     * Returns the value as a Carbon instance.
     *
     * @param mixed $value
     *
     * @return Carbon\Carbon
     *
     * @see Illuminate\Database\Eloquent\Model::asDateTime()
     */
    public function juggleDate($value);

    /**
     * Returns a string formated as ISO standard of 0000-00-00 00:00:00.
     *
     * @param mixed $value
     *
     * @return string
     */
    public function juggleDateTime($value);

    /**
     * Returns the date as a Unix timestamp.
     *
     * @param mixed $value
     *
     * @return int
     */
    public function juggleTimestamp($value);

    /**
     * Returns the value as a boolean.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function juggleBoolean($value);

    /**
     * Returns the value as an integer.
     *
     * @param mixed $value
     *
     * @return int
     */
    public function juggleInteger($value);

    /**
     * Returns the value as a float.
     *
     * @param mixed $value
     *
     * @return float
     */
    public function juggleFloat($value);

    /**
     * Returns the value as a string.
     *
     * @param mixed $value
     *
     * @return string
     */
    public function juggleString($value);

    /**
     * Returns the value as an array.
     *
     * @param mixed $value
     *
     * @return array
     */
    public function juggleArray($value);

    /**
     * Casts to null on empty.
     *
     * @param mixed $value
     *
     * @return mixed|null
     */
    public function juggleNull($value);
}
