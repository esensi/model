<?php namespace Esensi\Model\Traits;


/**
 * Trait that implements the Juggling Model Interface
 *
 * @package Esensi\Model
 * @author Diego Caprioli <diego@emersonmedia.com>
 * @copyright 2014 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 *
 * @see \Esensi\Model\Contracts\JugglingModelInterface
 */
trait JugglingModelTrait {

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
    public function __get($key)
    {
        $value = parent::__get($key);

        return $this->getDynamicJugglable($key, $value);
    }

    /**
     * Dynamically set attributes on the model.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function __set($key, $value)
    {
        parent::__set($key, $value);

        $this->setDynamicJugglable($key, $value);
    }

    /**
     * Overriden attributesToArray() Eloquent Model method,
     * to type juggle first, and then call the parent method.
     *
     * @return array
     * @see Illuminate\Database\Eloquent\Model::attributestoArray()
     */
    public function attributesToArray()
    {
        if ($this->juggling)
        {
            $this->juggleAttributes();
        }

        return parent::attributesToArray();
    }

    /**
     * If juggling is active, it returns the juggleAttribute.
     * If not it just returns the value as if was passed
     *
     * @param  string $key
     * @param  mixed $value
     * @return mixed
     */
    public function getDynamicJugglable( $key, $value )
    {
        if ($this->juggling)
        {
            return $this->juggle($key, $value); // no change to $attributes is made
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
    public function setDynamicJugglable( $key, $value )
    {
        // if juggling is enabled
        if ($this->juggling)
        {
            // and the key is configured to be juggled
            if ( $this->isJugglable($key) )
            {
                // and has value
                if ($value)
                {
                    // we need to juggle it
                    $this->juggleAttribute($key, $value);
                }

            }
        }
    }

    /**
     * Get the juggable attributes
     *
     * @return array
     */
    public function getJugglable()
    {
        return $this->jugglable;
    }

    /**
     * Set the jugglable attributes.
     *
     * @param  array $attributes to juggle
     * @return void
     */
    public function setJugglable( array $attributes )
    {
        $this->jugglable = $attributes;
    }

    /**
     * Add an attribute to the jugglable array.
     *
     * @example addJugglable( string $attribute, ... )
     * @param  string $attribute to juggle
     * @return void
     */
    public function addJugglable( $attribute )
    {
        $this->mergeJugglable( func_get_args() );
    }

    /**
     * Remove an attribute from the jugglable array.
     *
     * @example removeJugglable( string $attribute, ... )
     * @param  string $attribute to juggle
     * @return void
     */
    public function removeJugglable( $attribute )
    {
        $this->jugglable = array_diff( $this->jugglable, func_get_args() );
    }

    /**
     * Merge an array of attributes with the jugglable array.
     *
     * @param  array $attributes to juggle
     * @return void
     */
    public function mergeJugglable( array $attributes )
    {
        $this->jugglable = array_merge( $this->jugglable, $attributes );
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
        return $this->getJugglable()
            && array_key_exists( $attribute, $this->getJugglable() );
    }

    /**
     * Casts a value to the coresponding attribute type and sets
     * it on the attributes array of this model.
     *
     * @param  string $key
     * @param  string $value
     * @return  void
     */
    protected function juggleAttribute( $key, $value )
    {
        $jugglable = $this->getJugglable();

        $type = $jugglable[$key];

        // if the value is a date, we'll convert it from a DateTime
        // instance into a form proper for storage on the database tables using
        // the connection grammar's date format.
        // @see \Illuminate\Database\Eloquent\Model::fromDateTime()

        if (array_search($type, ['date', 'datetime', 'timestamp']) !== false)
        {
            $value = $this->fromDateTime($value);
        }
        else
        {
            $value = $this->juggle($key, $value);
        }

        $this->attributes[$key] = $value;
    }

    /**
     * Juggles all attributes that are configured to be juggled.
     *
     * @return void
     */
    protected function juggleAttributes()
    {
        // Iterate the juggable fields, and if the field is present
        // cast the attribute and replace within the array.
        foreach ($this->getJugglable() as $key => $type)
        {
            if ( isset($this->attributes[$key]) )
            {
                $this->juggleAttribute($key, $this->attributes[$key]);
            }
        }
    }

    /**
     * Cast the value to the attribute's type as specified in the juggable array.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function juggle( $type, $value )
    {

        if ( ! is_null($value) )
        {

            $type = strtolower($type);

            switch ($type) {

                case 'bool':
                    $normalizedType = 'boolean';
                    break;

                case 'int':
                    $normalizedType = 'integer';
                    break;

                case 'double':
                    $normalizedType = 'float';
                    break;

                case 'date':
                case 'datetime':
                case 'timestamp':
                case 'string':
                case 'float':
                case 'array':
                default:
                    $normalizedType = $type;
                    break;

            }

            $method = "juggle". studly_case($normalizedType);
            if ( !method_exists($this, $method) )
            {
                throw new InvalidArgumentException("The method $method is not defined.");
            }
            $value = $this->{$method}($key, $value);

        }

        return $value;

    }

    /**
     * Returns the value as a Carbon instance
     *
     * @param  mixed $value
     * @return \Carbon\Carbon
     * @see \Illuminate\Database\Eloquent\Model::asDateTime()
     */
    protected function juggleDate($value)
    {
        return $this->asDateTime($value);
    }

    /**
     * Returns a string formated as ISO standar for 0000-00-00 00:00:00
     *
     * @param  mixed $value
     * @return string
     */
    protected function juggleDatetime($value)
    {
        $carbon = $this->juggleDate($value);
        return $carbon->toDateTimeString();
    }

    /**
     * Returns the date as a Unix timestamp
     *
     * @param  mixed $value
     * @return int   Unix timestamp
     */
    protected function juggleTimestamp($value)
    {
        $carbon = $this->juggleDate($value);
        return $carbon->timestamp;
    }

    /**
     * Returns the value as boolena
     *
     * @param  mixed $value
     * @return boolean
     */
    protected function juggleBoolean($value)
    {
        return $this->juggleType('booelan', $value);
    }

    /**
     * Returns the value as integer
     *
     * @param  mixed $value
     * @return integer
     */
    protected function juggleInteger($value)
    {
        return $this->juggleType('integer', $value);
    }

    /**
     * Returns the value as float
     *
     * @param  mixed $value
     * @return float
     */
    protected function juggleFloat($value)
    {
        return $this->juggleType('float', $value);
    }

    /**
     * Returns the value as string
     *
     * @param  mixed $value
     * @return string
     */
    protected function juggleString($value)
    {
        return $this->juggleType('string', $value);
    }

    /**
     * Returns the value as array
     *
     * @param  mixed $value
     * @return array
     */
    protected function juggleArray($value)
    {
        return $this->juggleType('array', $value);
    }

    /**
     * Sets the type of the value and returns it.
     *
     * The possibles values of $type are (according to settype docs):
     * "boolean" (or, since PHP 4.2.0, "bool")
     * "integer" (or, since PHP 4.2.0, "int")
     * "float" (only possible since PHP 4.2.0, for older versions use the deprecated variant "double")
     * "string"
     * "array"
     * "object"
     * "null" (since PHP 4.2.0)
     *
     * @param  string $type
     * @param  mixed $value
     * @return mixed
     */
    protected function juggleType($type, $value)
    {
        return settype($value, $type); // the is_null($value) check is one in juggle()
    }



}
