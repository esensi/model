<?php

use \Esensi\Model\Model;
use \Mockery;
use \PHPUnit_Framework_TestCase as PHPUnit;


/**
 * Tests for the Validating Model Trait
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <dalabarge@emersonmedia.com>
 * @copyright 2014 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 *
 * @see \Watson\Validating\ValidatingTrait
 *      Uses ValidatingTrait. Assuming test coverage for bulk of functionality.
 */
class ValidatingModelTraitTest extends PHPUnit {

    /**
     * Set Up and Prepare Tests.
     */
    public function setUp()
    {
        // Mock the Model that uses the custom trait
        $this->model = Mockery::mock('ModelValidatingStub');
        $this->model->makePartial();
    }

    /**
     * Tear Down and Clean Up Tests.
     */
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * Simple verification that the inherited methods from the Watson trait are functional
     */
    public function testExistingGetAndSetRules()
    {
        $model_rules = $this->model->getRules();
        $new_rules   = [
            'name'   => ['alpha'],
            'number' => ['required', 'integer']
        ];
        $this->model->setRules($new_rules);

        $this->assertNotEquals($model_rules, $this->model->getRules());
        $this->assertEquals($new_rules, $this->model->getRules());
    }

    /**
     * Tests dynamic adding/overwriting of rules to $rules
     */
    public function testAddRulesAddsToRules()
    {
        /**
         * Test simple "Add 2 new rules"
         */
        $model_rules = $this->model->getRules();
        $new_rules   = [
            'foo' => ['min:20'],
            'bar' => ['alpha']
        ];
        $combined_rules = array_merge($model_rules, $new_rules);
        $this->model->addRules($new_rules);
        $this->assertSame($combined_rules, $this->model->getRules());

        /**
         * Test "Adding rule overwrites existing rule"
         */
        $model_rules = $this->model->getRules();
        $new_rules   = [
            'foo' => ['required']
        ];

        $this->assertArrayHasKey('foo', $model_rules);
        $this->assertNotEquals($new_rules['foo'], $model_rules['foo']);

        $this->model->addRules($new_rules);
        $current_rules = $this->model->getRules();
        $this->assertEquals($new_rules['foo'], $current_rules['foo']);

    }

    /**
     * Tests dynamic adding/overwriting of rules to $rulesets[$ruleset_name]
     */
    public function testAddRulesAddsToRuleset()
    {
        $ruleset_name = 'updating';
        /**
         * Test simple "Add 2 new rules to ruleset"
         */
        $model_rules = $this->model->getRuleset($ruleset_name);
        $new_rules   = [
            'foo' => ['min:20'],
            'bar' => ['alpha']
        ];
        $combined_rules = array_merge($model_rules, $new_rules);
        $this->model->addRules($new_rules, $ruleset_name);
        $this->assertSame($combined_rules, $this->model->getRuleset($ruleset_name));
    }

    /**
     * Tests dynamic removal of rules from $rules
     */
    public function testRemoveRulesRemovesFromRules()
    {
        $key_to_remove = 'name';
        $this->assertArrayHasKey($key_to_remove, $this->model->getRules());
        $this->model->removeRules([$key_to_remove]);
        $this->assertArrayNotHasKey($key_to_remove, $this->model->getRules());
    }

    /**
     * Tests dynamic removal of rules from $rules
     */
    public function testRemoveRulesRemovesFromRuleset()
    {
        $ruleset_name  = 'updating';
        $key_to_remove = 'name';
        /**
         * Test simple "Add 2 new rules to ruleset"
         */
        $model_rules = $this->model->getRuleset($ruleset_name);
        $this->assertArrayHasKey($key_to_remove, $this->model->getRuleset($ruleset_name));
        $this->model->removeRules([$key_to_remove], $ruleset_name);
        $this->assertArrayNotHasKey($key_to_remove, $this->model->getRuleset($ruleset_name));
    }

}

/**
 * Model Stub for Validating Tests
 */
class ModelValidatingStub extends Model {

    protected $rules = [
        'name'   => ['required', 'alpha'],
        'number' => ['integer']
    ];

    protected $rulesets = [
        'updating' => [
            'name' => ['alpha', 'min:3']
        ]
    ];

}