<?php

namespace Esensi\Model\Observers;

use Esensi\Model\Contracts\HashingModelInterface;

/**
 * Model observer for Hashing Model Trait.
 *
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2015-2016 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/license.md MIT License
 *
 * @link http://www.emersonmedia.com
 * @see Esensi\Model\Traits\HashingModelTrait
 */
class HashingModelObserver
{
    /**
     * Register an event listener for the creating event.
     * Listener hashes the hashable attributes before save.
     *
     * @param Esensi\Model\Contracts\HashingModelInterface $model
     */
    public function creating(HashingModelInterface $model)
    {
        $this->performHashing($model, 'creating');
    }

    /**
     * Register an event listener for the updating event.
     * Listener hashes the hashable attributes before save.
     *
     * @param Esensi\Model\Contracts\HashingModelInterface $model
     */
    public function updating(HashingModelInterface $model)
    {
        $this->performHashing($model, 'updating');
    }

    /**
     * Check if hashing is enabled and then hash the attributes
     * that need hashing.
     *
     * @param Esensi\Model\Contracts\HashingModelInterface $model
     * @param string                                       $event name
     */
    protected function performHashing(HashingModelInterface $model, $event)
    {
        if ($model->getHashing()) {
            $model->hashAttributes();
        }
    }
}
