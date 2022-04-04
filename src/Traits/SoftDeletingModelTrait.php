<?php

namespace Esensi\Model\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * Trait that implements the Soft Deleting Model Interface.
 *
 */
trait SoftDeletingModelTrait
{
    /*
     * Use Illuminate's trait as a base.
     *
     * @see Illuminate\Database\Eloquent\SoftDeletes
     */
    use SoftDeletes;

    /**
     * We want to boot our own observer so we stub out this
     * boot method. This renders this function void.
     */
    public static function bootSoftDeletes()
    {
    }

    /**
     * Boot the trait's observers.
     */
    public static function bootSoftDeletingModelTrait()
    {
        static::addGlobalScope(new SoftDeletingScope());
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
        $defaults = [static::CREATED_AT, static::UPDATED_AT, $this->getDeletedAtColumn()];

        return array_merge($this->dates, $defaults);
    }
}
