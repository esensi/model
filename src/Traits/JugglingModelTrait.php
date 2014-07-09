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
    * Attributes that are to be casted to a different type.
    *
    * @var array
    */
    protected $jugglable = [];

    /**
     * Whether the model is juggling attributes or not.
     *
     * @var boolean
     */
    protected $juggling = false;


    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        $value = parent::__get($key);
        if ($this->juggling)
        {
            return $this->getJuggledAttribute($key, $value);
        }
        return $value;
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
        if ($this->juggling)
        {
            $this->setJuggledAttribute($key, $value); //changes $attributes[$key]
        }
        parent::__set($key, $this->atributes[$key]);
    }


    /**
     * Get the juggable attributes
     *
     * @return array
     */
    public function getJugglable()
    {
        // This block allows for backwards compatibility with $dates by
        // merging values present in the original date array with our
        // defaults.
        $originals = array();
        foreach ($this->dates as $key)
        {
            $originals[$key] = 'date';
        }

        $defaults = array(static::CREATED_AT => 'date', static::UPDATED_AT => 'date');

        return array_merge($this->jugglable, $originals, $defaults);
    }


    /**
     * Set the jugglable attributes.
     *
     * @param  array $attributes to purge
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
     * @param  string $attribute to purge
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
     * @param  string $attribute to purge
     * @return void
     */
    public function removeJugglable( $attribute )
    {
        $this->jugglable = array_diff( $this->jugglable, func_get_args() );
    }

    /**
     * Merge an array of attributes with the jugglable array.
     *
     * @param  array $attributes to purge
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
     * Returns whether the attribute is purgeable.
     *
     * @param string $attribute name
     * @return boolean
     */
    public function isJugglable( $attribute )
    {
        return $this->getJugglable()
            && in_array( $attribute, $this->getJugglable() );
    }



    /**
     * Casts a value to the coresponding attribute type and sets
     * it on the attributes array of this model
     *
     * @param  string $key
     * @param  string $value
     * @return  void
     */
    protected function juggleAttribute( $key, $value )
    {
        $this->attributes[$key] = $this->juggle($key, $value);
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
        $jugglable = $this->getJugglable();
        foreach ($jugglable as $key => $type)
        {
            if ( !isset($this->attributes[$key]) )
            {
                continue;
            }
            $this->juggleAttribute($key, $this->attributes[$key]);
        }
    }


    /**
     * Cast the value to a the attribute's type as specified iun the juggable array
     *
     * @param  string $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function juggle( $key, $value )
    {

        // if the attribute exists in our jugglables array
        // we need to juggle it
        if ( array_key_exists($key, $jugglable = $this->getJugglable()) !== false )
        {

            $type = $jugglable[$key];
            $type = strtolower($type);

            switch ($type) {

                case 'date':
                    $value = $this->juggleDate($value);
                    break;

                case 'string':
                case 'bool':
                case 'double':
                case 'int':
                    $value = $this->juggleToType($type, $value);
                    break;

                default:
                    $method = "juggle". studly_case($type);
                    $value = $this->{$method}($key, $value);
                    break;

            }

        }

        return $value;

    }



    protected function juggleDate($value)
    {
        if ($value)
        {
            $value = $this->asDateTime($value);
            settype($value, 'date');
        }
        return $value;
    }



    protected function juggleToType($type, $value)
    {
        if ($value !== null)
        {
            settype($value, $type);
        }
        return $value;
    }




    /**
     * Gets the attribute juggled value.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getJuggledAttribute( $key, $value )
    {
        return $this->juggle($key, $value); //no change to $attributes is made
    }



    /**
     * Sets the attribute value with the corresponding juggled value
     *
     * @param   string $key
     * @param   string $value
     * @return  void
     */
    public function setJuggledAttribute( $key, $value )
    {

        // if the attribute exists in our jugglables array
        // we need to juggle it
        if ( array_key_exists($key, $jugglable = $this->getJugglable()) !== false )
        {

            if ($value)
            {
                // if the value is a date though, we'll convert it from a DateTime
                // instance into a form proper for storage on the database tables using
                // the connection grammar's date format.
                if ($jugglable[$key] === 'date')
                {
                    $value = $this->fromDateTime($value);
                }
                else
                {
                    $value = $this->juggle($key, $value);
                }
            }

            // set the value and return;
            $this->attributes[$key] = $value;
            return;

        }

    }


    /**
     * Overriden attributesToArray() Eloquent Model method, to juggle
     * first any juggable property, and then call the parent method.
     *
     * @return array
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
     * options to implement this trait:
     *
     * Dinamically add setXAttribute and getXAttribute to the class
     * for each of the jugglable properties. For this, the $jugglable
     * must be static, so it can be accessed from the bootTraits method.
     * Every method that access the values for the properties, checks if
     * there is any mutator, and calls it. This way we don't override the
     * Model, but add dinamically new code (the get set mutators) using the
     * runkit_method_add PHP method.
     *
     * Another option is to just override every method where the Model
     * accesses the attributes and do the juggling there (this is the
     * approach followed by Daryl's hack)
     *
     * other option is injecting middleware into the _get and _set methods
     * like relatingmodeltrait rather than overwrite everything.
     * Overwrite the arrayattributes method but put all the logic in a
     * helper method so you just call the helper if juggling is enabled
     * and then call the parent method after.
     *
     */



    /**
     * Convert the model's attributes to an array.
     *
     * @todo We can omit this method by creating a dynamic mutator "getXAttribute($value)"
     * for each of the juggable fields. Each of these methods should call juggleAttribute
     *
     * @return array
     */
    /*public function attributesToArray()
    {
        $attributes = $this->getArrayableAttributes();

        // Iterate the juggable fields, and if the field is present
        // cast the attribute and replace within the array.
        foreach ($this->getJugglables() as $key => $type)
        {
            if ( ! isset($attributes[$key])) continue;

            $attributes[$key] = $this->juggleAttribute($type, $attributes[$key]);
        }

        // We want to spin through all the mutated attributes for this model and call
        // the mutator for the attribute. We cache off every mutated attributes so
        // we don't have to constantly check on attributes that actually change.
        foreach ($this->getMutatedAttributes() as $key)
        {
            if ( ! array_key_exists($key, $attributes)) continue;

            $attributes[$key] = $this->mutateAttributeForArray(
                $key, $attributes[$key]
            );
        }

        // Here we will grab all of the appended, calculated attributes to this model
        // as these attributes are not really in the attributes array, but are run
        // when we need to array or JSON the model for convenience to the coder.
        foreach ($this->appends as $key)
        {
            $attributes[$key] = $this->mutateAttributeForArray($key, null);
        }

        return $attributes;
    }*/


    /**
     * Get a plain attribute (not a relationship).
     *
     * @todo We can omit this method by creating a dynamic mutator "getXAttribute($value)"
     * for each of the juggable fields. Each of these methods should call juggleAttribute
     *
     * @param  string  $key
     * @return mixed
     */
    /*protected function getAttributeValue($key)
    {
        $value = $this->getAttributeFromArray($key);

        // If the attribute has a get mutator, we will call that then return what
        // it returns as the value, which is useful for transforming values on
        // retrieval from the model to a form that is more useful for usage.
        if ($this->hasGetMutator($key))
        {
            return $this->mutateAttribute($key, $value);
        }

        // If the field is present in the juggable array, then
        // retrieve the intended type, and return the casted
        // value.
        elseif (array_key_exists($key, $jugglable = $this->getJugglables()))
        {
            if ($value) return $this->juggleAttribute($jugglable[$key], $value);
        }

        return $value;
    }*/



    /**
     * Set a given attribute on the model.
     *
     * @todo We can omit this method by creating a dynamic mutator "setXAttribute($value)"
     * for each of the juggable fields. Each of these methods should call juggleAttribute.
     * If setting a 'date', dont call juggle, but fromDateTime instead
     *
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    /*public function setAttribute($key, $value)
    {
        // First we will check for the presence of a mutator for the set operation
        // which simply lets the developers tweak the attribute as it is set on
        // the model, such as "json_encoding" an listing of data for storage.
        if ($this->hasSetMutator($key))
        {
            $method = 'set'.studly_case($key).'Attribute';

            return $this->{$method}($value);
        }

        // If the attribute being set is held within the castable array
        // then cast before setting the attribute. Date types are handled
        // seperately due to being complex objects.
        elseif (array_key_exists($key, $jugglable = $this->getJugglables()))
        {
            if ($value)
            {
                if ($jugglable[$key] === 'date')
                {
                    $value = $this->fromDateTime($value);
                }
                else
                {
                    $value = $this->juggleAttribute($jugglable[$key], $value);
                }
            }
        }

        $this->attributes[$key] = $value;
    }*/





}
