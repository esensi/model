<?php namespace Esensi\Model\Traits;

use \Illuminate\Database\Eloquent\ModelNotFoundException;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use \Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Trait that implements the RelatingModelInterface
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <wishlist@emersonmedia.com>
 * @copyright 2014 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 *
 * @see \Esensi\Model\Contracts\RelatingModelInterface
 */
trait RelatingModelTrait {

    /**
     * Return the relationship configurations
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
     * Return whether the name is a relationship or not
     *
     * @param string $name of related model
     * @return boolean
     */
    public function isRelationship( $name )
    {
        return array_key_exists( $name, $this->relationships );
    }

    /**
     * Proxy call a relationship method using the
     * configuration arguments of the relationship
     *
     * @param string $name of related model
     * @return mixed
     */
    function callRelationship( $name )
    {
        $args = $this->getRelationship( $name );

        $method = array_shift( $args );
        return call_user_func_array( [ $this, $method ], $args );
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

            $name = snake_case();
        }

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

}
