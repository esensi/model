<?php

namespace Esensi\Model\Traits;

use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Trait that implements the Soft Deleting Model Interface
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2015 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 *
 * @see \Esensi\Model\Contracts\SoftDeletingModelInterface
 */
trait SoftDeletingModelTrait
{
    /**
     * Use Illuminate's trait as a base.
     *
     * @see \Illuminate\Database\Eloquent\SoftDeletes
     */
    use SoftDeletes;

    /**
     * We want to boot our own observer so we stub out this
     * boot method. This renders this function void.
     *
     * @return void
     */
    public static function bootSoftDeletingTrait(){ }

    /**
     * Boot the trait's observers.
     *
     * @return void
     */
    public static function bootSoftDeletingModelTrait()
    {
        static::addGlobalScope(new SoftDeletingScope);
    }

    /**
     * Get the attributes that should be converted to dates.
     *
     * Overwriting this method here allows the developer to
     * extend the dates using the $dates property without
     * needing to maintain the "deleted_at" column.
     *
     * @return array
     */
    public function getDates()
    {
        $defaults = array(static::CREATED_AT, static::UPDATED_AT, $this->getDeletedAtColumn());

        return array_merge($this->dates, $defaults);
    }

}
