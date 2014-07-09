<?php namespace Esensi\Model\Contracts;

/**
 * Juggling Model Interface
 *
 * @package Esensi\Model
 * @author Diego Caprioli <diego@emersonmedia.com>
 * @copyright 2014 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 */
interface JugglingModelInterface {

    /**
     * Overriden attributesToArray() Eloquent Model method, to juggle
     * first any juggable property, and then call the parent method.
     *
     * @return array
     */
    function attributesToArray();

    /**
     * Cast an attribute to a new type.
     *
     * @param  string $type
     * @param  mixed  $value
     * @return mixed
     */
    function juggleAttribute($type, $value);

    /**
     * Get the attributes that should be cast upon retrieval.
     *
     * @return array
     */
    function getJugglables();

}
