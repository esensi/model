<?php

namespace Esensi\Model\Observers;

use Illuminate\Database\Eloquent\Model;
use Watson\Validating\ValidatingObserver;

/**
 * Model observer for Validating Model Trait.
 *
 * @deprecated In watson/validating@0.10.9 the custom methods
 *             used below were deprecated in favor of Laravel 5's
 *             form request validation classes. Stop using
 *             rulesets right now as they will be removed
 * @see Esensi\Model\Traits\ValidatingModelTrait
 * @see Watson\Validating\ValidatingObserver
 */
class ValidatingModelObserver extends ValidatingObserver
{
    /**
     * Register the validation event for creating the model.
     *
     * @param Illuminate\Database\Eloquent\Model  $model
     *
     * @return bool
     */
    public function creating(Model $model)
    {
        if ($model->getRuleset('creating')) {
            return $this->performValidation($model, 'creating');
        }
    }

    /**
     * Register the validation event for updating the model.
     *
     * @param Illuminate\Database\Eloquent\Model  $model
     *
     * @return bool
     */
    public function updating(Model $model)
    {
        if ($model->getRuleset('updating')) {
            return $this->performValidation($model, 'updating');
        }
    }

    /**
     * Register the validation event for saving the model. Saving validation
     * should only occur if creating and updating validation does not.
     *
     * @param Illuminate\Database\Eloquent\Model  $model
     *
     * @return bool
     */
    public function saving(Model $model)
    {
        if (! $model->getRuleset('creating') && ! $model->getRuleset('updating')) {
            return $this->performValidation($model, 'saving');
        }
    }

    /**
     * Register the validation event for deleting the model.
     *
     * @param Illuminate\Database\Eloquent\Model $model
     *
     * @return bool
     */
    public function deleting(Model $model)
    {
        if ($model->getRuleset('deleting')) {
            return $this->performValidation($model, 'deleting');
        }
    }

    /**
     * Register the validation event for restoring the model.
     *
     * @param Illuminate\Database\Eloquent\Model  $model
     *
     * @return bool
     */
    public function restoring(Model $model)
    {
        if ($model->getRuleset('restoring')) {
            return $this->performValidation($model, 'restoring');
        }
    }

    /**
     * Perform validation with the specified ruleset.
     *
     * @param Illuminate\Database\Eloquent\Model  $model
     * @param string $event
     *
     * @return bool
     */
    protected function performValidation(Model $model, $event)
    {
        // If the model has validating enabled, perform it.
        if ($model->getValidating()) {
            // Fire the namespaced validating event and prevent validation
            // if it returns a value.
            if ($this->fireValidatingEvent($model, $event) !== null) {
                return;
            }

            // Fire the validating failed event.
            if ($model->isValid($event) === false) {
                $this->fireValidatedEvent($model, 'failed');
                if ($model->getThrowValidationExceptions()) {
                    $model->throwValidationException();
                }

                return false;
            }

            // Fire the validating.passed event.
            $this->fireValidatedEvent($model, 'passed');

        // Fire the validating.skipped event.
        } else {
            $this->fireValidatedEvent($model, 'skipped');
        }
    }
}
