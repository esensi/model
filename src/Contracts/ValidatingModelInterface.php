<?php

namespace Esensi\Model\Contracts;

use Watson\Validating\ValidatingInterface;

/**
 * Validating Model Interface.
 *
 */
interface ValidatingModelInterface extends ValidatingInterface
{
    /**
     * Get the default ruleset for any event. Will first search to see if a
     * 'saving' ruleset exists, fallback to '$rules' and otherwise return
     * an empty array.
     *
     * @deprecated watson/validating@0.10.9
     *
     * @return array
     */
    public function getDefaultRules();

    /**
     * Get all the rulesets.
     *
     * @deprecated watson/validating@0.10.9
     *
     * @return array
     */
    public function getRulesets();

    /**
     * Set all the rulesets.
     *
     * @deprecated watson/validating@0.10.9
     *
     * @param array  $rulesets
     */
    public function setRulesets(array $rulesets = null);

    /**
     * Get a ruleset, and merge it with saving if required.
     *
     * @deprecated watson/validating@0.10.9
     *
     * @param string  $ruleset
     * @param bool  $mergeWithSaving
     *
     * @return array
     */
    public function getRuleset($ruleset, $mergeWithSaving = false);

    /**
     * Set the rules used for a particular ruleset.
     *
     * @deprecated watson/validating@0.10.9
     *
     * @param array  $rules
     * @param string  $ruleset
     */
    public function setRuleset(array $rules, $ruleset);

    /**
     * Add rules to the existing rules or ruleset, overriding any existing.
     *
     * @deprecated watson/validating@0.10.9
     *
     * @param array  $rules
     * @param string  $ruleset
     */
    public function addRules(array $rules, $ruleset = null);

    /**
     * Remove rules from the existing rules or ruleset.
     *
     * @deprecated watson/validating@0.10.9
     *
     * @param mixed  $keys
     * @param string  $ruleset
     */
    public function removeRules($keys, $ruleset = null);

    /**
     * Helper method to merge rulesets, with later rules overwriting
     * earlier ones.
     *
     * @deprecated watson/validating@0.10.9
     *
     * @param array  $keys
     *
     * @return array
     */
    public function mergeRulesets($keys);

    /**
     * Returns whether the model is valid or not.
     *
     * @param mixed  $ruleset (@deprecated watson/validating@0.10.9)
     * @param bool  $mergeWithSaving (@deprecated watson/validating@0.10.9)
     *
     * @return bool
     */
    public function isValid($ruleset = null, $mergeWithSaving = true);

    /**
     * Returns if the model is valid, otherwise throws an exception.
     *
     * @param  string  $ruleset (@deprecated watson/validating@0.10.9)
     *
     * @throws Watson\Validating\ValidationException
     *
     * @return bool
     */
    public function isValidOrFail($ruleset = null);

    /**
     * Returns whether the model is invalid or not.
     *
     * @param mixed  $ruleset (@deprecated watson/validating@0.10.9)
     * @param bool  $mergeWithSaving (@deprecated watson/validating@0.10.9)
     *
     * @return bool
     */
    public function isInvalid($ruleset = null, $mergeWithSaving = true);

    /**
     * Update the unique rules of the given ruleset to
     * include the model identifier.
     *
     * @deprecated watson/validating@0.10.9
     *
     * @param string $ruleset
     */
    public function updateRulesetUniques($ruleset = null);
}
