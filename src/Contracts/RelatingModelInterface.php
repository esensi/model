<?php namespace Esensi\Model\Contracts;

/**
 * Relating Model Interface
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <wishlist@emersonmedia.com>
 * @copyright 2014 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 */
interface RelatingModelInterface {

    /**
     * Call a dynamically resolved relationship.
     *
     * @param  string $name
     * @return mixed
     */
    function callDynamicRelationship( $name );

    /**
     * Get a dynamically resolved relationship.
     *
     * @param  string $name
     * @return mixed
     */
    function getDynamicRelationship( $name );

    /**
     * Return the relationship configurations.
     *
     * @param string $name of related model
     * @return array
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getRelationship( $name );

    /**
     * Return whether the name is a relationship or not.
     *
     * @param string $name of related model
     * @return boolean
     */
    public function isRelationship( $name );

    /**
     * Proxy call a relationship method using the
     * configuration arguments of the relationship.
     *
     * @param string $name of related model
     * @return mixed
     */
    function callRelationship( $name );

}
