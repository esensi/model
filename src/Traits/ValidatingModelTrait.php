<?php

namespace Esensi\Model\Traits;

use Esensi\Model\Observers\ValidatingModelObserver;
use Watson\Validating\ValidatingTrait;

/**
 * Trait that implements the Validating Model Interface
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2015 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 *
 * @deprecated In watson/validating@0.10.9 the custom methods
 *             used below were deprecated in favor of Laravel 5's
 *             form request validation classes. Stop using
 *             rulesets right now as they will be removed.
 *
 * @see \Esensi\Model\Contracts\ValidatingModelInterface
 */
trait ValidatingModelTrait
{
    /**
     * Use Watson's trait as a base.
     *
     * @see \Watson\Validating\ValidatingTrait
     */
    use ValidatingTrait;

    /**
     * We want to boot our own observer so we stub out this
     * boot method. This renders this function void.
     *
     * @return void
     */
    public static function bootValidatingTrait(){ }

    /**
     * Boot the trait's observers.
     *
     * @return void
     */
    public static function bootValidatingModelTrait()
    {
        static::observe(new ValidatingModelObserver);
    }

    /**
     * Get the default ruleset for any event. Will first search to see if a
     * 'saving' ruleset exists, fallback to '$rules' and otherwise return
     * an empty array
     *
     * @deprecated watson/validating@0.10.9
     * @return array
     */
    public function getDefaultRules()
    {
        $rules = $this->getRuleset('saving', false) ?: $this->getRules();
        return $rules ?: [];
    }

    /**
     * Get all the rulesets.
     *
     * @deprecated watson/validating@0.10.9
     * @return array
     */
    public function getRulesets()
    {
        return $this->rulesets ?: [];
    }

    /**
     * Set all the rulesets.
     *
     * @deprecated watson/validating@0.10.9
     * @param  array $rulesets
     * @return void
     */
    public function setRulesets(array $rulesets = null)
    {
        $this->rulesets = $rulesets;
    }

    /**
     * Get a ruleset, and merge it with saving if required.
     *
     * @deprecated watson/validating@0.10.9
     * @param  string $ruleset
     * @param  bool   $mergeWithSaving
     * @return array
     */
    public function getRuleset($ruleset, $mergeWithSaving = false)
    {
        $rulesets = $this->getRulesets();
        if (array_key_exists($ruleset, $rulesets))
        {
            // If the ruleset exists and merge with saving is true, return
            // the rulesets merged.
            if ($mergeWithSaving)
            {
                return $this->mergeRulesets(['saving', $ruleset]);
            }
            // If merge with saving is not true then simply retrun the ruleset.
            return $rulesets[$ruleset];
        }
        // If the ruleset requested does not exist but merge with saving is true
        // attempt to return
        else if ($mergeWithSaving)
        {
            return $this->getDefaultRules();
        }
    }

    /**
     * Set the rules used for a particular ruleset.
     *
     * @deprecated watson/validating@0.10.9
     * @param  array  $rules
     * @param  string $ruleset
     * @return void
     */
    public function setRuleset(array $rules, $ruleset)
    {
        $this->rulesets[$ruleset] = $rules;
    }

    /**
     * Add rules to the existing rules or ruleset, overriding any existing.
     *
     * @deprecated watson/validating@0.10.9
     * @param  array   $rules
     * @param  string  $ruleset
     * @return void
     */
    public function addRules(array $rules, $ruleset = null)
    {
        if ($ruleset)
        {
            $newRules = array_merge($this->getRuleset($ruleset), $rules);
            $this->setRuleset($newRules, $ruleset);
        }
        else
        {
            $newRules = array_merge($this->getRules(), $rules);
            $this->setRules($newRules);
        }
    }

    /**
     * Remove rules from the existing rules or ruleset.
     *
     * @deprecated watson/validating@0.10.9
     * @param  mixed   $keys
     * @param  string  $ruleset
     * @return void
     */
    public function removeRules($keys, $ruleset = null)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        $rules = $ruleset ? $this->getRuleset($ruleset) : $this->getRules();
        array_forget($rules, $keys);
        if ($ruleset)
        {
            $this->setRuleset($rules, $ruleset);
        }
        else
        {
            $this->setRules($rules);
        }
    }

    /**
     * Helper method to merge rulesets, with later rules overwriting
     * earlier ones
     *
     * @deprecated watson/validating@0.10.9
     * @param  array $keys
     * @return array
     */
    public function mergeRulesets($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        $rulesets = [];
        foreach ($keys as $key)
        {
            $rulesets[] = (array) $this->getRuleset($key, false);
        }
        return array_filter(call_user_func_array('array_merge', $rulesets));
    }

    /**
     * Returns whether the model is valid or not.
     *
     * @param  mixed $ruleset (@deprecated watson/validating@0.10.9)
     * @param  bool  $mergeWithSaving (@deprecated watson/validating@0.10.9)
     * @return bool
     */
    public function isValid($ruleset = null, $mergeWithSaving = true)
    {
        $rules = is_array($ruleset) ? $ruleset : $this->getRuleset($ruleset, $mergeWithSaving) ?: $this->getDefaultRules();
        return $this->performValidation($rules);
    }

    /**
     * Returns if the model is valid, otherwise throws an exception.
     *
     * @param  string $ruleset (@deprecated watson/validating@0.10.9)
     * @return bool
     * @throws \Watson\Validating\ValidationException
     */
    public function isValidOrFail($ruleset = null)
    {
        if ( ! $this->isValid($ruleset))
        {
            $this->throwValidationException();
        }
        return true;
    }

    /**
     * Returns whether the model is invalid or not.
     *
     * @param  mixed  $ruleset (@deprecated watson/validating@0.10.9)
     * @param  bool   $mergeWithSaving (@deprecated watson/validating@0.10.9)
     * @return bool
     */
    public function isInvalid($ruleset = null, $mergeWithSaving = true)
    {
        return ! $this->isValid($ruleset, $mergeWithSaving);
    }

    /**
     * Update the unique rules of the given ruleset to
     * include the model identifier.
     *
     * @deprecated watson/validating@0.10.9
     * @param  string $ruleset
     * @return void
     */
    public function updateRulesetUniques($ruleset = null)
    {
        $rules = $this->getRuleset($ruleset);
        $this->setRuleset($ruleset, $this->injectUniqueIdentifierToRules($rules));
    }

}
