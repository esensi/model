<?php

namespace Esensi\Model\Contracts;

/**
 * Relating Model Interface.
 *
 */
interface RelatingModelInterface
{
    /**
     * Return the relationship configurations.
     *
     * @param string  $name of related model
     *
     * @throws Illuminate\Database\Eloquent\ModelNotFoundException
     *
     * @return array
     */
    public function getRelationship($name);

    /**
     * Return whether the name is a relationship or not.
     *
     * @param string  $name of related model
     *
     * @return bool
     */
    public function isRelationship($name);
}
