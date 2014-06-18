<?php namespace Esensi\Model\Observers;

use \Esensi\Model\Model;

/**
 * Model observer for HashingModelTrait
 *
 * @author daniel <daniel@bexarcreative.com>
 * @see \Esensi\Model\Traits\HashingModelTrait
 */
class HashingModelObserver {

    /**
     * Register an event listener for the creating event.
     * Listener hashes the hashable attributes before save.
     *
     * @param \Esensi\Model\Model $model
     * @return void
     */
    public function creating( Model $model )
    {
        $this->performHashing( $model, 'creating' );
    }

    /**
     * Register an event listener for the updating event.
     * Listener hashes the hashable attributes before save.
     *
     * @param \Esensi\Model\Model $model
     * @return void
     */
    public function updating( Model $model )
    {
        $this->performHashing( $model, 'updating' );
    }

    /**
     * Check if hashing is enabled and then hash the attributes
     * that need hashing.
     *
     * @param \Esensi\Model\Model $model
     * @param string $event name
     * @return void
     */
    protected function performHashing( Model $model, $event )
    {
        if( $model->getHashing() )
        {
            $model->hashAttributes();
        }
    }

}
