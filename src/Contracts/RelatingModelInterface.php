<?php

namespace Esensi\Model\Contracts;

/**
 * Relating Model Interface
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2015 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 */
interface RelatingModelInterface
{
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

}
