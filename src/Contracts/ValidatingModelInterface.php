<?php namespace Esensi\Model\Contracts;

/**
 * Validating Model Interface
 *
 * @author daniel <daniel@bexarcreative.com>
 */
interface ValidatingModelInterface {

    /**
     * Returns whether or not the model will attempt to
     * validate itself before saving
     *
     * @return boolean
     */
    public function getValidating();

    /**
     * Set whether or not the model will attempt to validate
     * itself before saving
     *
     * @todo make this a public method
     * @param  boolean
     * @return void
     */
    function setValidating( $value );

    /**
     * Returns whether or not the model will throw exceptions
     * when validation fails
     *
     * @return boolean
     */
    public function getThrowValidationExceptions();

    /**
     * Set whether or not the model will throw exceptions
     * when validation fails
     *
     * @param  boolean
     * @return void
     */
    public function setThrowValidationExceptions( $value );

    /**
     * Returns whether or not the model will add its unique
     * identifier to the unique rules when validating
     *
     * @return boolean
     */
    public function getInjectUniqueIdentifier();

    /**
     * Set whether or not the model will add its unique
     * identifier to the unique rules when validating
     *
     * @param  boolean
     * @return void
     */
    public function setInjectUniqueIdentifier( $value );

    /**
     * Get the model
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel();

    /**
     * Get all the validation rules
     *
     * @return array
     */
    public function getRules();

    /**
     * Set all the validation rules
     *
     * @todo strong type the array $rules
     * @param  array $rules
     * @return void
     */
    public function setRules( $rules );

    /**
     * Get a validation ruleset if it exists
     *
     * @param  string $ruleset name
     * @return array
     */
    public function getRuleset( $ruleset );

    /**
     * Set a validation ruleset
     *
     * @todo strong type the array $rules
     * @param  array $rules for ruleset
     * @param  string $ruleset (optional) name
     * @return void
     */
    public function setRuleset( $rules, $ruleset = 'saving');

    /**
     * Get the custom validation messages being used by the model
     *
     * @return array
     */
    public function getMessages();

    /**
     * Set the custom validation messages to be used by the model
     *
     * @todo strong type the array $message
     * @param  array $messages
     * @return void
     */
    public function setMessages( $messages );

    /**
     * Get the validation error message for the model
     *
     * @return array
     */
    public function getErrors();

    /**
     * Returns whether the model is validat or not
     *
     * @param string $ruleset name
     * @return boolean
     */
    public function isValid( $ruleset = 'saving' );

    /**
     * Returns whether the model is invalid or not
     *
     * @param string $ruleset name
     * @return boolean
     */
    public function isInvalid( $ruleset = 'saving' );

    /**
     * Force the model to be saved without validating
     *
     * @return boolean
     */
    public function forceSave( );

    /**
     * Perform a one-off save that will raise an exception
     * on failed validation even if the model normally
     * would not throw an exception when saving
     *
     * @return void
     */
    public function saveWithException();

    /**
     * Perform a one-off save that will return a boolean
     * value representing the validation state instead
     * of raising an exception even if the model normally
     * would throw an exception when saving
     *
     * @return boolean
     */
    public function saveWithoutException();

    /**
     * Validate the model against its rules, returning
     * whether or not it passes and setting the error messages
     * on the model if required
     *
     * @todo make this a public method
     * @param string $ruleset (optional) name
     * @return boolean
     * @throws ValidationException
     */
    function validate( $ruleset = 'saving' );

    /**
     * Update the unique rules so they work for this particular model
     *
     * @param string $ruleset (optional) name
     * @return string
     */
    public function updateUniqueRules( $ruleset = 'saving' );

}
