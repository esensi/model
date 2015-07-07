<?php

namespace Esensi\Model;

use Esensi\Model\Contracts\EncryptingModelInterface;
use Esensi\Model\Contracts\HashingModelInterface;
use Esensi\Model\Contracts\JugglingModelInterface;
use Esensi\Model\Contracts\PurgingModelInterface;
use Esensi\Model\Contracts\RelatingModelInterface;
use Esensi\Model\Contracts\ValidatingModelInterface;
use Esensi\Model\Traits\EncryptingModelTrait;
use Esensi\Model\Traits\HashingModelTrait;
use Esensi\Model\Traits\JugglingModelTrait;
use Esensi\Model\Traits\PurgingModelTrait;
use Esensi\Model\Traits\RelatingModelTrait;
use Esensi\Model\Traits\ValidatingModelTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Contracts\Support\MessageProvider;

/**
 * Base Model
 *
 * @package Esensi\Model
 * @author Diego Caprioli <diego@emersonmedia.com>
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2015 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/LICENSE.txt MIT License
 * @link http://www.emersonmedia.com
 *
 * @see \Illuminate\Database\Eloquent\Model
 * @see \Esensi\Model\Contracts\EncryptingModelInterface
 * @see \Esensi\Model\Contracts\HashingModelInterface
 * @see \Esensi\Model\Contracts\JugglingModelInterface
 * @see \Esensi\Model\Contracts\PurgingModelInterface
 * @see \Esensi\Model\Contracts\RelatingModelInterface
 * @see \Esensi\Model\Contracts\ValidatingModelInterface
 * @see \Illuminate\Contracts\Support\MessageProvider
 */
abstract class Model extends Eloquent implements
    EncryptingModelInterface,
    HashingModelInterface,
    JugglingModelInterface,
    MessageProvider,
    PurgingModelInterface,
    RelatingModelInterface,
    ValidatingModelInterface
{
    /**
     * Make model validate attributes.
     *
     * This comes first even though it is out of alphabetical
     * order because it is important that it is booted before
     * hashing and purging traits.
     *
     * @see \Esensi\Model\Traits\ValidatingModelTrait
     */
    use ValidatingModelTrait;

    /**
     * Make model encrypt attributes.
     *
     * @see \Esensi\Model\Traits\EncryptingModelTrait
     */
    use EncryptingModelTrait;

    /**
     * Make model hash attributes.
     *
     * @see \Esensi\Model\Traits\HashingModelTrait
     */
    use HashingModelTrait;

    /**
     * Make the model juggle attributes when setting and getting
     *
     * @see \Esensi\Model\Contracts\JugglingModelInterface
     */
    use JugglingModelTrait;

    /**
     * Make model purge attributes.
     *
     * @see \Esensi\Model\Traits\PurgingModelTrait
     */
    use PurgingModelTrait;

    /**
     * Make model use properties for model relationships.
     *
     * @see \Esensi\Model\Traits\RelatingModelTrait
     */
    use RelatingModelTrait;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [];

    /**
     * The default rules that the model will validate against.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * The rulesets that the model will validate against.
     *
     * @deprecated watson/validating 0.10.9
     * @var array
     */
    protected $rulesets = [];

    /**
     * The attributes to encrypt when set and
     * decrypt when gotten.
     *
     * @var array
     */
    protected $encryptable = [];

    /**
     * The attributes to hash before saving.
     *
     * @var array
     */
    protected $hashable = [];

    /**
     * Attributes to cast to a different type.
     *
     * @var array
     */
    protected $jugglable = [];

    /**
     * The attributes to purge before saving.
     *
     * @var array
     */
    protected $purgeable = [];

    /**
     * Relationships that the model should set up.
     *
     * @var array
     */
    protected $relationships = [];

    /**
     * Extra attributes to be added to pivot relationships.
     *
     * @var array
     */
    protected $relationshipPivots = [];

    /**
     * Dynamically retrieve attributes.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get( $key )
    {
        // Resolve relationship dynamically
        if( $relationship = $this->getDynamicRelationship( $key ) )
        {
            return $relationship;
        }

        // Dynamically retrieve the encryptable attribute
        if( $attribute = $this->getDynamicEncrypted( $key ) )
        {
            $value = $attribute;
        }

        // Fallback to the default Eloquent dynamic getter
        else
        {
            $value = parent::__get( $key );
        }

        // Dynamically juggle the attribute.
        // This is always called so that even decrypted values
        // can be casted after decrypting.
        return $this->getDynamicJuggle( $key, $value );
    }

    /**
     * Dynamically set attributes.
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function __set( $key, $value )
    {
        // Dynamically set the encryptable attribute
        if( $this->setDynamicEncryptable( $key, $value ) )
        {
            return;
        }

        // Fallback to the default Eloquent dynamic setter
        parent::__set( $key, $value );

        // Dynamically juggle the attribute.
        $this->setDynamicJuggle( $key, $value );
    }

    /**
     * Get the messages for the instance.
     *
     * @return \Illuminate\Contracts\Support\MessageBag
     */
    public function getMessageBag()
    {
        return $this->getErrors();
    }

}
