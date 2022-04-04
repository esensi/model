<?php

namespace Esensi\Model\Observers;

use Esensi\Model\Contracts\PurgingModelInterface;

/**
 * Model observer for Purging Model Trait.
 *
 */
class PurgingModelObserver
{
    /**
     * Register an event listener for the creating event.
     * Listener purgees the purgeable attributes before save.
     *
     * @param Esensi\Model\Contracts\PurgingModelInterface  $model
     */
    public function creating(PurgingModelInterface $model)
    {
        $this->performPurging($model, 'creating');
    }

    /**
     * Register an event listener for the updating event.
     * Listener purgees the purgeable attributes before save.
     *
     * @param Esensi\Model\Contracts\PurgingModelInterface  $model
     */
    public function updating(PurgingModelInterface $model)
    {
        $this->performPurging($model, 'updating');
    }

    /**
     * Check if purging is enabled and then purge the attributes
     * that need purging.
     *
     * @param Esensi\Model\Contracts\PurgingModelInterface  $model
     * @param string  $event name
     */
    protected function performPurging(PurgingModelInterface $model, $event)
    {
        if ($model->getPurging()) {
            $model->purgeAttributes();
        }
    }
}
