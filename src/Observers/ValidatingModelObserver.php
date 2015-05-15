<?php namespace Esensi\Model\Observers;

use \Illuminate\Database\Eloquent\Model;
use \Watson\Validating\ValidatingObserver;

/**
 * Model observer for Validating Model Trait
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <dalabarge@emersonmedia.com>
 * @copyright 2014 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 *
 * @see \Esensi\Model\Traits\ValidatingModelTrait
 * @see \Watson\Validating\ValidatingObserver
 */
class ValidatingModelObserver extends ValidatingObserver {

    /**
     * Register the validation event for creating the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return boolean
     */
    public function creating(Model $model)
    {
        if ($model->getRuleset('creating'))
        {
            return $this->performValidation($model, 'creating');
        }
    }

    /**
     * Register the validation event for updating the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return boolean
     */
    public function updating(Model $model)
    {
        if ($model->getRuleset('updating'))
        {
            return $this->performValidation($model, 'updating');
        }
    }

    /**
     * Register the validation event for saving the model. Saving validation
     * should only occur if creating and updating validation does not.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return boolean
     */
    public function saving(Model $model)
    {
        if ( ! $model->getRuleset('creating') && ! $model->getRuleset('updating'))
        {
            return $this->performValidation($model, 'saving');
        }
    }

    /**
     * Register the validation event for deleting the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return boolean
     */
    public function deleting(Model $model)
    {
        if ($model->getRuleset('deleting'))
        {
            return $this->performValidation($model, 'deleting');
        }
    }

    /**
     * Register the validation event for restoring the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return boolean
     */
    public function restoring(Model $model)
    {
        if ($model->getRuleset('restoring'))
        {
            return $this->performValidation($model, 'restoring');
        }
    }

}
