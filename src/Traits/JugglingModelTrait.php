<?php

namespace Esensi\Model\Traits;

use Carbon\Carbon;
use InvalidArgumentException;

/**
 * Trait that implements the Juggling Model Interface
 *
 * @package Esensi\Model
 * @author Diego Caprioli <diego@emersonmedia.com>
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2015 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 *
 * @see \Esensi\Model\Contracts\JugglingModelInterface
 */
trait JugglingModelTrait
{
    /**
     * Whether the model is type juggling attributes or not.
     *
     * @var boolean
     */
    protected $juggling = true;

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get( $key )
    {
        // Get the value from the default Eloquent method
        $value = parent::__get( $key );

        // Dynamically get the juggled value
        return $this->getDynamicJuggle( $key, $value );
    }

    /**
     * Dynamically set attributes on the model.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function __set( $key, $value )
    {
        // Set the attribute value using the default Eloquent method
        parent::__set( $key, $value );

        // Dynamically set the juggled value
        $this->setDynamicJuggle( $key, $this->attribute[ $key ] );
    }

    /**
     * Override attributesToArray() Eloquent Model method,
     * to type juggle first, and then call the parent method.
     *
     * @return array
     * @see \Illuminate\Database\Eloquent\Model::attributestoArray()
     */
    public function attributesToArray()
    {
        // Check if juggling is enabled
        if ( $this->getJuggling() )
        {
            // Juggle all the jugglable attributes
            $this->juggleAttributes();
        }

        // Fallback to default Eloquent method
        return parent::attributesToArray();
    }

    /**
     * If juggling is active, it returns the juggleAttribute.
     * If not it just returns the value as it was passed.
     *
     * @param  string $key
     * @param  mixed $value
     * @return mixed
     */
    protected function getDynamicJuggle( $key, $value )
    {
        if ( $this->isJugglable( $key ) )
        {
            return $this->juggle( $value, $this->getJuggleType( $key ) );
        }

        return $value;
    }

    /**
     * If juggling is active, it sets the attribute in the model
     * by type juggling the value first.
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    protected function setDynamicJuggle( $key, $value )
    {
        // Check that the attribute is jugglable
        if ( ! is_null( $value ) && $this->isJugglable( $key ) )
        {
            // Cast the value to the type set for the attribute
            $this->juggleAttribute( $key, $value );
        }
    }

    /**
     * Get the juggable attributes.
     *
     * @return array
     */
    public function getJugglable()
    {
        return $this->jugglable ? $this->jugglable : [];
    }

    /**
     * Set the jugglable attributes.
     *
     * @param  array $attributes to juggle
     * @return void
     * @throws \InvalidArgumentException
     */
    public function setJugglable( array $attributes )
    {
        // Check that each of the types are indeed jugglable types
        foreach( $attributes as $attribute => $type)
        {
            $this->checkJuggleType( $type );
        }

        // Set the juggle attributes
        $this->jugglable = $attributes;
    }

    /**
     * Add an attribute to the jugglable array.
     *
     * @param  string $attribute
     * @param  string $type
     * @return void
     * @throws \InvalidArgumentException
     */
    public function addJugglable( $attribute, $type )
    {
        $this->mergeJugglable( [ $attribute => $type ] );
    }

    /**
     * Remove an attribute or several attributes from the jugglable array.
     *
     * @example removeJugglable( string $attribute, ... )
     * @param  mixed $attributes
     * @return void
     */
    public function removeJugglable( $attributes )
    {
        // Make sure we are dealing an associative array
        if ( ! is_array($attributes) )
        {
            $attributes = func_get_args();
        }
        $attributes = array_flip( $attributes );

        // Get the remaining jugglables
        $jugglables = array_diff_key($this->getJugglable(), $attributes);

        // Set the remaining jugglables
        $this->setJugglable( $jugglables );
    }

    /**
     * Merge an array of attributes with the jugglable array.
     *
     * @param  array $attributes to juggle
     * @return void
     * @throws \InvalidArgumentException
     */
    public function mergeJugglable( array $attributes )
    {
        $this->setJugglable( array_merge( $this->getJugglable(), $attributes ) );
    }

    /**
     * Returns whether or not the model will juggle attributes.
     *
     * @return boolean
     */
    public function getJuggling()
    {
        return $this->juggling;
    }

    /**
     * Set whether or not the model will juggle attributes.
     *
     * @param  boolean
     * @return void
     */
    public function setJuggling( $value )
    {
        $this->juggling = (bool) $value;
    }

    /**
     * Returns whether the attribute is type jugglable.
     *
     * @param string $attribute name
     * @return boolean
     */
    public function isJugglable( $attribute )
    {
        return $this->getJuggling()
            && array_key_exists( $attribute, $this->getJugglable() );
    }

    /**
     * Returns whether the type is a type that can be juggled to.
     *
     * @param string $type to cast
     * @return boolean
     */
    public function isJuggleType( $type )
    {
        // Construct a normalized juggle method from the type
        $method = $this->buildJuggleMethod( $type );

        // Any type that does map to a model method is invalid
        if ( ! method_exists($this, $method) )
        {
            return false;
        }

        return true;
    }

    /**
     * Checks whether the type is a type that can be juggled to.
     *
     * @param string $type to cast
     * @return boolean
     * @throws \InvalidArgumentException
     */
    public function checkJuggleType( $type )
    {
        // If not valid, throw an exception.
        if ( ! $this->isJuggleType( $type ) )
        {
            throw new InvalidArgumentException("The type \"" . $type . "\" is not a valid type cast.");
            return false;
        }

        return true;
    }

    /**
     * Build the method name that the type normalizes to.
     *
     * @param string $type to cast
     * @return string
     */
    public function buildJuggleMethod( $type )
    {
        // Convert type to acceptable pattern
        $type = lcfirst( studly_case( $type ) );

        // Map the type to it's normalized type
        switch ($type) {

            case 'bool':
            case 'boolean':
                $normalizedType = 'boolean';
                break;

            case 'int':
            case 'integer':
                $normalizedType = 'integer';
                break;

            case 'float':
            case 'double':
                $normalizedType = 'float';
                break;

            case 'datetime':
            case 'dateTime':
                $normalizedType = 'dateTime';
                break;

            case 'date':
            case 'timestamp':
            case 'string':
            case 'array':
            default:
                $normalizedType = $type;
                break;
        }

        // Construct a dynamic method name
        return "juggle". studly_case($normalizedType);
    }

    /**
     * Gets the type that the attribute will be casted to.
     *
     * @param string $attribute
     * @return string
     */
    public function getJuggleType( $attribute )
    {
        $jugglable = $this->getJugglable();
        return $jugglable[ $attribute ];
    }

    /**
     * Juggles all attributes that are configured to be juggled.
     *
     * @return void
     */
    public function juggleAttributes()
    {
        // Iterate the juggable fields, and if the field is present
        // cast the attribute and replace within the attributes array.
        foreach( $this->getJugglable() as $attribute => $type )
        {
            if ( isset($this->attributes[ $attribute ]) )
            {
                $this->juggleAttribute( $attribute, $this->attributes[ $attribute ] ) ;
            }
        }
    }

    /**
     * Casts a value to the coresponding attribute type and sets
     * it on the attributes array of this model.
     *
     * @param string $attribute
     * @param string $value
     * @return void
     */
    public function juggleAttribute( $attribute, $value )
    {
        $type = $this->getJuggleType( $attribute );
        $this->attributes[ $attribute ] = $this->juggle( $value, $type );
    }

    /**
     * Cast the value to the attribute's type as specified in the juggable array.
     *
     * @param  mixed  $value
     * @param  string $type
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function juggle( $value, $type )
    {
        // Cast non-null values
        if ( ! is_null($value) )
        {
            // Ensure that the type is a valid type to cast.
            // We do this check here because it might not have been done
            // as is the case when the model is first initialized.
            if( $this->checkJuggleType( $type ) )
            {
                // Get the method that the type maps to
                $method = $this->buildJuggleMethod( $type );

                // Cast the value to the type using the method
                $value = $this->{$method}( $value );
            }
        }

        return $value;
    }

    /**
     * Returns the value as a Carbon instance.
     *
     * @param  mixed $value
     * @return \Carbon\Carbon
     * @see \Illuminate\Database\Eloquent\Model::asDateTime()
     */
    public function juggleDate( $value )
    {
        // Short circuit if value is already a Carbon date
        if ( $value instanceof Carbon )
        {
            return $value;
        }

        // Use Eloquent helper function to convert it to a Carbon date.
        // The $value must be a format that asDateTime can parse.
        // Errors like "InvalidArgumentException: Unexpected data found."
        // mean you don't have a parseable format.
        return $this->asDateTime( $value );
    }

    /**
     * Returns a string formated as ISO standard of 0000-00-00 00:00:00.
     *
     * @param  mixed $value
     * @return string
     */
    public function juggleDateTime( $value )
    {
        // Ensure we have a Carbon date to work with
        $carbon = $this->juggleDate( $value );

        // Convert the Carbon date to the format
        return $carbon->toDateTimeString();
    }

    /**
     * Returns the date as a Unix timestamp.
     *
     * @param  mixed $value
     * @return integer
     */
    public function juggleTimestamp( $value )
    {
        // Ensure we have a Carbon date to work with
        $carbon = $this->juggleDate( $value);

        // Convert the Carbon date to the format
        return $carbon->timestamp;
    }

    /**
     * Returns the value as a boolean.
     *
     * @param  mixed $value
     * @return boolean
     */
    public function juggleBoolean( $value )
    {
        return $this->juggleType( $value, 'boolean' );
    }

    /**
     * Returns the value as an integer.
     *
     * @param  mixed $value
     * @return integer
     */
    public function juggleInteger( $value )
    {
        return $this->juggleType( $value, 'integer' );
    }

    /**
     * Returns the value as a float.
     *
     * @param  mixed $value
     * @return float
     */
    public function juggleFloat( $value )
    {
        return $this->juggleType( $value, 'float' );
    }

    /**
     * Returns the value as a string.
     *
     * @param  mixed $value
     * @return string
     */
    public function juggleString( $value )
    {
        return $this->juggleType( $value, 'string' );
    }

    /**
     * Returns the value as an array.
     *
     * @param  mixed $value
     * @return array
     */
    public function juggleArray( $value )
    {
        return $this->juggleType( $value, 'array' );
    }

    /**
     * Casts the value to the type. Possibles types are:
     *     boolean, integer, float, string, array, object, null
     * @link http://php.net/manual/en/function.settype.php
     *
     * @param  mixed $value
     * @param  string $type (optional)
     * @return mixed
     */
    protected function juggleType( $value, $type = "null" )
    {
        settype( $value, $type );
        return $value;
    }

}
