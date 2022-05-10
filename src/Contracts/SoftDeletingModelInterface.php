<?php

namespace Esensi\Model\Contracts;

/**
 * Soft Deleting Model Interface.
 *
 */
interface SoftDeletingModelInterface
{
    /**
     * Force a hard delete on a soft deleted model.
     */
    public function forceDelete();

    /**
     * Restore a soft-deleted model instance.
     *
     * @return bool|null
     */
    public function restore();

    /**
     * Determine if the model instance has been soft-deleted.
     *
     * @return bool
     */
    public function trashed();

    /**
     * Register a restoring model event with the dispatcher.
     *
     * @param Closure|string  $callback
     */
    public static function restoring($callback);

    /**
     * Register a restored model event with the dispatcher.
     *
     * @param Closure|string  $callback
     */
    public static function restored($callback);

    /**
     * Get the name of the "deleted at" column.
     *
     * @return string
     */
    public function getDeletedAtColumn();

    /**
     * Get the fully qualified "deleted at" column.
     *
     * @return string
     */
    public function getQualifiedDeletedAtColumn();
}
