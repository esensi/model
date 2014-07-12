<?php namespace Esensi\Model\Traits;

use \Esensi\Model\Observers\ValidatingModelObserver;
use \Watson\Validating\ValidatingTrait;

/**
 * Trait that implements the Validating Model Interface
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <dalabarge@emersonmedia.com>
 * @copyright 2014 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 *
 * @see \Esensi\Model\Contracts\ValidatingModelInterface
 */
trait ValidatingModelTrait {

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
     * Add rules to the default $rules or a specified $rulesets[$ruleset]
     *
     * @param array   $add_rules
     * @param string  $ruleset
     * @param boolean $merge_with_saving
     * @return void
     */
    public function addRules(array $add_rules, $ruleset = null, $merge_with_saving = false)
    {
        if ($ruleset)
        {
            $combined_rules = array_merge($this->getRuleset($ruleset, $merge_with_saving), $add_rules);
            $this->setRuleset($combined_rules, $ruleset);
            return;
        }

        $combined_rules = array_merge($this->getRules(), $add_rules);
        $this->setRules($combined_rules);
    }

    /**
     * Remove rules from the default $rules or a specified $rulesets[$ruleset]
     *
     * @param array   $remove_rules_keys
     * @param string  $ruleset
     * @param boolean $merge_with_saving
     * @return void
     */
    public function removeRules(array $remove_rules_keys, $ruleset = null, $merge_with_saving = false)
    {
        if ($ruleset)
        {
            $rules = $this->getRuleset($ruleset, $merge_with_saving);
            foreach ($remove_rules_keys as $key)
            {
                if (array_key_exists($key, $rules)) unset($rules[$key]);
            }
            $this->setRuleset($rules, $ruleset);
            return;
        }

        $rules = $this->getRules();
        foreach ($remove_rules_keys as $key)
        {
            if (array_key_exists($key, $rules)) unset($rules[$key]);
        }
        $this->setRules($rules);
    }

}
