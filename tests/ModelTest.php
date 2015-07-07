<?php

use Esensi\Model\Model;
use PHPUnit_Framework_TestCase as PHPUnit;

/**
 * Tests for the Model
 *
 * @package Esensi\Model
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2014 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 */
class ModelTest extends PHPUnit
{
    /**
     * Set Up and Prepare Tests.
     */
    public function setUp()
    {
        // Mock the Model that uses the custom traits
        $this->model = Mockery::mock('ModelStub');
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
     * Test that the Model uses the traits and in the right order.
     */
    public function testModelUsesTraits()
    {
        // Get the traits off the model
        $traits = function_exists('class_uses_recursive') ?
            class_uses_recursive(get_class($this->model)) : class_uses(get_class($this->model));

        // Check Model uses the Validating trait
        $this->assertContains('Esensi\Model\Traits\ValidatingModelTrait', $traits);

        // Check Model uses the Encrypting trait
        $this->assertContains('Esensi\Model\Traits\EncryptingModelTrait', $traits);

        // Check Model uses the Hashing trait
        $this->assertContains('Esensi\Model\Traits\HashingModelTrait', $traits);

        // Check Model uses the Juggling trait
        $this->assertContains('Esensi\Model\Traits\JugglingModelTrait', $traits);

        // Check Model uses the Purging trait
        $this->assertContains('Esensi\Model\Traits\PurgingModelTrait', $traits);

        // Check Model uses the Relating trait
        $this->assertContains('Esensi\Model\Traits\RelatingModelTrait', $traits);

        // Check Validating trait comes before Hashing and Purging traits
        $traitValues = array_values($traits);
        $validatingIndex = array_search('Esensi\Model\Traits\ValidatingModelTrait', $traitValues);
        $hashingIndex = array_search('Esensi\Model\Traits\HashingModelTrait', $traitValues);
        $purgingIndex = array_search('Esensi\Model\Traits\PurgingModelTrait', $traitValues);
        $this->assertLessThan($hashingIndex, $validatingIndex);
        $this->assertLessThan($purgingIndex, $validatingIndex);
    }

    /**
     * Test that the Model implements the interfaces.
     */
    public function testModelImplementsInterfaces()
    {
        // Check Model implements the Validating interface
        $this->assertInstanceOf('\Esensi\Model\Contracts\ValidatingModelInterface', $this->model);

        // Check Model implements the Encrypting interface
        $this->assertInstanceOf('\Esensi\Model\Contracts\EncryptingModelInterface', $this->model);

        // Check Model implements the Hashing interface
        $this->assertInstanceOf('\Esensi\Model\Contracts\HashingModelInterface', $this->model);

        // Check Model implements the Juggling interface
        $this->assertInstanceOf('\Esensi\Model\Contracts\JugglingModelInterface', $this->model);

        // Check Model implements the Purging interface
        $this->assertInstanceOf('\Esensi\Model\Contracts\PurgingModelInterface', $this->model);

        // Check Model implements the Relating interface
        $this->assertInstanceOf('\Esensi\Model\Contracts\RelatingModelInterface', $this->model);
    }

}

/**
 * Model Stub for Model Tests
 */
class ModelStub extends Model
{

}
