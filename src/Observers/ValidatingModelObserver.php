<?php namespace Esensi\Model\Observers;

use \Esensi\Model\Model;
use \Watson\Validating\ValidatingObserver;

/**
 * Model observer for ValidatingModelTrait
 *
 * @author daniel <daniel@bexarcreative.com>
 * @see \Esensi\Model\Traits\ValidatingModelTrait
 * @see \Watson\Validating\ValidatingObserver
 */
class ValidatingModelObserver extends ValidatingObserver{

    /**
     * Register an event listener for the restoring event.
     * Listener validates against the restoring ruleset.
     *
     * @param \Esensi\Model\Model $model
     * @return boolean
     */
    public function restoring( Model $model )
    {
        return $this->performValidation( $model, 'restoring' );
    }

}
