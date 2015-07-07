<?php

namespace Esensi\Model\Observers;

use Esensi\Model\Contracts\PurgingModelInterface;

/**
 * Model observer for Purging Model Trait
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2015 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 *
 * @see \Esensi\Model\Traits\PurgingModelTrait
 */
class PurgingModelObserver
{
    /**
     * Register an event listener for the creating event.
     * Listener purgees the purgeable attributes before save.
     *
     * @param \Esensi\Model\Contracts\PurgingModelInterface $model
     * @return void
     */
    public function creating( PurgingModelInterface $model )
    {
        $this->performPurging( $model, 'creating' );
    }

    /**
     * Register an event listener for the updating event.
     * Listener purgees the purgeable attributes before save.
     *
     * @param \Esensi\Model\Contracts\PurgingModelInterface $model
     * @return void
     */
    public function updating( PurgingModelInterface $model )
    {
        $this->performPurging( $model, 'updating' );
    }

    /**
     * Check if purging is enabled and then purge the attributes
     * that need purging.
     *
     * @param \Esensi\Model\Contracts\PurgingModelInterface $model
     * @param string $event name
     * @return void
     */
    protected function performPurging( PurgingModelInterface $model, $event )
    {
        if( $model->getPurging() )
        {
            $model->purgeAttributes();
        }
    }

}
