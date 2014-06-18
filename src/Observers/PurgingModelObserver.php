<?php namespace Esensi\Model\Observers;

use \Esensi\Model\Model;

/**
 * Model observer for PurgingModelTrait
 *
 * @author daniel <daniel@bexarcreative.com>
 * @see \Esensi\Model\Traits\PurgingModelTrait
 */
class PurgingModelObserver {

    /**
     * Register an event listener for the creating event.
     * Listener purgees the purgeable attributes before save.
     *
     * @param \Esensi\Model\Model $model
     * @return void
     */
    public function creating( Model $model )
    {
        $this->performPurging( $model, 'creating' );
    }

    /**
     * Register an event listener for the updating event.
     * Listener purgees the purgeable attributes before save.
     *
     * @param \Esensi\Model\Model $model
     * @return void
     */
    public function updating( Model $model )
    {
        $this->performPurging( $model, 'updating' );
    }

    /**
     * Check if purging is enabled and then purge the attributes
     * that need purging.
     *
     * @param \Esensi\Model\Model $model
     * @param string $event name
     * @return void
     */
    protected function performPurging( Model $model, $event )
    {
        if( $model->getPurging() )
        {
            $model->purgeAttributes();
        }
    }

}
