<?php namespace Esensi\Model\Contracts;

use \Watson\Validating\ValidatingInterface;

/**
 * Validating Model Interface
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <dalabarge@emersonmedia.com>
 * @copyright 2014 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 *
 * @see \Watson\Validating\ValidatingInterface
 */
interface ValidatingModelInterface extends ValidatingInterface {

    /**
     * Get the default ruleset for any event. Will first search to see if a
     * 'saving' ruleset exists, fallback to '$rules' and otherwise return
     * an empty array
     *
     * @return array
     */
    public function getDefaultRules();

    /**
     * Get all the rulesets.
     *
     * @return array
     */
    public function getRulesets();

    /**
     * Set all the rulesets.
     *
     * @param  array $rulesets
     * @return void
     */
    public function setRulesets(array $rulesets = null);

    /**
     * Get a ruleset, and merge it with saving if required.
     *
     * @param  string $ruleset
     * @param  bool   $mergeWithSaving
     * @return array
     */
    public function getRuleset($ruleset, $mergeWithSaving = false);

    /**
     * Set the rules used for a particular ruleset.
     *
     * @param  array  $rules
     * @param  string $ruleset
     * @return void
     */
    public function setRuleset(array $rules, $ruleset);

    /**
     * Add rules to the existing rules or ruleset, overriding any existing.
     *
     * @param  array   $rules
     * @param  string  $ruleset
     * @return void
     */
    public function addRules(array $rules, $ruleset = null);

    /**
     * Remove rules from the existing rules or ruleset.
     *
     * @param  mixed   $keys
     * @param  string  $ruleset
     * @return void
     */
    public function removeRules($keys, $ruleset = null);

    /**
     * Helper method to merge rulesets, with later rules overwriting
     * earlier ones
     *
     * @param  array $keys
     * @return array
     */
    public function mergeRulesets($keys);

    /**
     * Returns whether the model is valid or not.
     *
     * @param  mixed $ruleset
     * @param  bool  $mergeWithSaving
     * @return bool
     */
    public function isValid($ruleset = null, $mergeWithSaving = true);

    /**
     * Returns if the model is valid, otherwise throws an exception.
     *
     * @param  string $ruleset
     * @return bool
     * @throws \Watson\Validating\ValidationException
     */
    public function isValidOrFail($ruleset = null);

    /**
     * Returns whether the model is invalid or not.
     *
     * @param  string $ruleset
     * @param  bool   $mergeWithSaving
     * @return bool
     */
    public function isInvalid($ruleset = null, $mergeWithSaving = true);

    /**
     * Update the unique rules of the given ruleset to
     * include the model identifier.
     *
     * @param  string $ruleset
     * @return void
     */
    public function updateRulesetUniques($ruleset = null);

}
