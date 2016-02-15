<?php

namespace Esensi\Model\Contracts;

/**
 * Relating Model Interface.
 *
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2015-2016 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/license.md MIT License
 *
 * @link http://www.emersonmedia.com
 */
interface RelatingModelInterface
{
    /**
     * Return the relationship configurations.
     *
     * @param string $name of related model
     *
     * @throws Illuminate\Database\Eloquent\ModelNotFoundException
     *
     * @return array
     */
    public function getRelationship($name);

    /**
     * Return whether the name is a relationship or not.
     *
     * @param string $name of related model
     *
     * @return bool
     */
    public function isRelationship($name);
}
