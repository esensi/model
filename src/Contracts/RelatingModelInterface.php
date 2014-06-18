<?php namespace Esensi\Model\Contracts;

/**
 * Relating Model Interface
 *
 * @author daniel <daniel@bexarcreative.com>
 */
interface RelatingModelInterface {

    /**
     * Return the relationship configurations
     *
     * @param string $name of related model
     * @return array
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getRelationship( $name );

    /**
     * Return whether the name is a relationship or not
     *
     * @param string $name of related model
     * @return boolean
     */
    public function isRelationship( $name );

    /**
     * Proxy call a relationship method using the
     * configuration arguments of the relationship
     *
     * @param string $name of related model
     * @return mixed
     */
    function callRelationship( $name );

}
