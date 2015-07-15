<?php

namespace Esensi\Model\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Trait that implements the Relating Model Interface
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2015 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 *
 * @see \Esensi\Model\Contracts\RelatingModelInterface
 */
trait RelatingModelTrait
{
    /**
     * Dynamically call methods.
     *
     * @param  string $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call( $method, $parameters )
    {
        // Resolve relationship dynamically
        if( $relationship = $this->callDynamicRelationship( $method ) )
        {
            return $relationship;
        }

        // Default Eloquent dynamic caller
        return parent::__call($method, $parameters);
    }

    /**
     * Dynamically retrieve attributes.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get( $key )
    {
        // Resolve relationship dynamically
        if( $relationship = $this->getDynamicRelationship( $key ) )
        {
            return $relationship;
        }

        // Default Eloquent dynamic getter
        return parent::__get( $key );
    }

    /**
     * Call a dynamically resolved relationship.
     *
     * @param  string $name
     * @return mixed
     */
    protected function callDynamicRelationship( $name )
    {
        // Dynamically call the relationship
        if ( $this->isRelationship( $name ) )
        {
            return $this->callRelationship( $name );
        }
    }

    /**
     * Get a dynamically resolved relationship.
     *
     * @param  string $name
     * @return mixed
     */
    protected function getDynamicRelationship( $name )
    {
        // Dynamically get the relationship
        if ( $this->isRelationship( $name ) )
        {
            // Use the relationship already loaded
            if ( array_key_exists( $name, $this->getRelations() ) )
            {
                return $this->getRelation( $name );
            }

            // Load the relationship
            return $this->getRelationshipFromMethod($name, camel_case($name));
        }
    }

    /**
     * Get the relationships.
     *
     * @return array
     */
    public function getRelationships()
    {
        return $this->relationships ?: [];
    }

    /**
     * Return the relationship configurations.
     *
     * @param string $name of related model
     * @return array
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getRelationship( $name )
    {
        // If relationship does not exist throw an exception
        if ( ! $this->isRelationship( $name ) )
        {
            $exception = new ModelNotFoundException();
            $exception->setModel( $name );
            throw $exception;
        }
        return $this->relationships[ $name ];
    }

    /**
     * Return the relationship configurations.
     *
     * @param string $name of related model
     * @return array
     */
    public function getPivotAttributes( $name )
    {
        return $this->relationshipPivots[ $name ] ?: [];
    }

    /**
     * Return whether the name is a relationship or not.
     *
     * @param string $name of related model
     * @return boolean
     */
    public function isRelationship( $name )
    {
        return array_key_exists( $name, $this->relationships );
    }

    /**
     * Return whether the relationshpi has pivot attributes or not.
     *
     * @param string $name of related model
     * @return boolean
     */
    public function hasPivotAttributes( $name )
    {
        return array_key_exists( $name, $this->relationshipPivots );
    }

    /**
     * Proxy call a relationship method using the
     * configuration arguments of the relationship.
     *
     * @param string $name of related model
     * @return mixed
     */
    protected function callRelationship( $name )
    {
        // Get the relationship arguments
        $args = $this->getRelationship( $name );

        // Build the relationship
        $method = array_shift( $args );
        $relationship = call_user_func_array( [ $this, $method ], $args );

        // Check to see if this relationship has extended pivot attributes
        if( $this->hasPivotAttributes( $name ) )
        {
            // Add timestamps to relationship
            $attributes = $this->getPivotAttributes( $name );
            if( in_array('timestamps', $attributes) )
            {
                unset($attributes[array_search('timestamps', $attributes)]);
                $relationship->withTimestamps();
            }

            // Add the pivot attributes to the relationship
            $relationship->withPivot( $attributes );
        }

        return $relationship;
    }

    /**
     * Define an inverse one-to-one or many relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $otherKey
     * @param  string  $relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function belongsTo( $related, $foreignKey = null, $otherKey = null, $relation = null )
    {
        // If no relation name was given, we will use this debug backtrace to extract
        // the calling method's name and use that as the relationship name as most
        // of the time this will be what we desire to use for the relatinoships.
        if ( is_null( $relation ) )
        {
            list(, $caller, $backtrace) = debug_backtrace(false);

            // Use custom relationship bindings
            if ( $backtrace['function'] == 'callRelationship' )
            {
                $relation = $backtrace['args'][0];
            }

            // or default to the Eloquent bindings
            else
            {
                $relation = $caller['function'];
            }
        }

        // If no foreign key was supplied, we can use a backtrace to guess the proper
        // foreign key name by using the name of the relationship function, which
        // when combined with an "_id" should conventionally match the columns.
        if ( is_null( $foreignKey ) )
        {
            $foreignKey = snake_case($relation) . '_id';
        }

        $instance = new $related;

        // Once we have the foreign key names, we'll just create a new Eloquent query
        // for the related models and returns the relationship instance which will
        // actually be responsible for retrieving and hydrating every relations.
        $query = $instance->newQuery();

        $otherKey = $otherKey ?: $instance->getKeyName();

        return new BelongsTo( $query, $this, $foreignKey, $otherKey, $relation );
    }

    /**
     * Define an polymorphic, inverse one-to-one or many relationship.
     *
     * @param  string $name
     * @param  string $type
     * @param  string $id
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function morphTo( $name = null, $type = null, $id = null )
    {
        // If no name is provided, we will use the backtrace to get the function name
        // since that is most likely the name of the polymorphic interface. We can
        // use that to get both the class and foreign key that will be utilized.
        if (is_null($name))
        {
            list(, $caller, $backtrace) = debug_backtrace(false);

            // Use custom relationship bindings
            if ($backtrace['function'] == 'callRelationship')
            {
                $relation = $backtrace['args'][0];
            }

            // or default to the Eloquent bindings
            else
            {
                $relation = $caller['function'];
            }

            $name = snake_case($relation);
        }

        list($type, $id) = $this->getMorphs($name, $type, $id);

        // If the type value is null it is probably safe to assume we're eager loading
        // the relationship. When that is the case we will pass in a dummy query as
        // there are multiple types in the morph and we can't use single queries.
        if ( is_null($class = $this->$type) )
        {
            return new MorphTo(
                $this->newQuery(), $this, $id, null, $type, $name
            );
        }

        // If we are not eager loading the relationship we will essentially treat this
        // as a belongs-to style relationship since morph-to extends that class and
        // we will pass in the appropriate values so that it behaves as expected.
        else
        {
            $instance = new $class;

            return new MorphTo(
                with($instance)->newQuery(), $this, $id, $instance->getKeyName(), $type, $name
            );
        }
    }

    /**
     * Set the relationships that should not be eager loaded.
     *
     * @param  Illuminate\Database\Query\Builder $query
     * @param  mixed $relations
     * @return $this
     */
    public function scopeWithout( $query, $relations )
    {
        $relations = is_array($relations) ? $relations : array_slice(func_get_args(), 1);
        $relationships = array_dot($query->getEagerLoads());
        foreach($relations as $relation)
        {
            unset($relationships[$relation]);
        }
        return $query->setEagerLoads([])->with(array_keys($relationships));
    }

}
