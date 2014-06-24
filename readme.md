## Esensi Model Traits Package

An [Esensi](https://github.com/esensi) package, coded by [Emerson Media](http://www.emersonmedia.com).

> **Want to work with us on great Laravel applications?**
Email us at [careers@emersonmedia.com](http://emersonmedia.com/contact)

The `Esensi/Model` package is just one package that makes up [Esensi](https://github.com/esensi), a platform built on [Laravel](http://laravel.com). This package bundles [PHP traits](http://php.net/traits) that extend Laravel's default Eloquent models and traits. Using traits allows for a high-degree of code reusability and extensibility. While this package provides some reasonable base models, as a developer you're free to mix and match traits in any combination you need, being confident that the code complies to a reliable interface and is properly unit tested. For more details on the inner workings of the traits please consult the generously documented source code.

> **Have a project in mind?** _Email us at [sales@emersonmedia.com](http://emersonmedia.com/contact), or call 1.877.439.6665._

### Quick Start

> **Notice:** _This code is specifically designed to be compatible with the [Laravel Framework](http://laravel.com) and may not be compatible as a stand-alone dependency or as part of another framework._

#### Add the Package to Composer

You will need to add the `esensi/model` package as a dependency of you application. From the command line, using [Composer](https://getcomposer.org), you can require it like so:

```bash
php composer.phar require esensi/modal 0.3.*
```

Or manually you can add it to your `composer.json` file:

```json
{
    "require": {
        "esensi/model": "0.3.*"
    }
}
```

Then be sure to run `php composer.phar update` to install the dependencies.

#### Extend the Default Model

Let's say you wanted to create a simple blog with a `Post` model. You could just extend the base `Esensi\Model\Model` and have `Post` model automatically handle validation, purging, hashing, encrypting, and even simplified relationship binding:

```php
<?php

use \Esensi\Model\Model;

class Post extends Model {
  
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'posts';

}
```

> **Tip:** Take a look at the generously commented [`Esensi\Model\Model` source code](https://github.com/esensi/model/blob/master/src/Model.php) for details on how to use individual traits without extending the default model.

#### Use Soft Deletes Too

Maybe you would like your blog to use Laravel's soft deletes on your `Post` model allowing you to trash and restore your articles. Simply swap out the `Esensi\Model\Model` with the soft deleting version `Esensi\Model\SoftModel` like so:

```php
<?php

use \Esensi\Model\SoftModel;

class Post extends SoftModel {

}
```

> **Tip:** While Laravel includes `SoftDeletingTrait`, Esensi expands upon this by also forcing the trait to comply with a [`SoftDeletingModelInterface` contract](https://github.com/esensi/model/blob/0.3/src/Contracts/SoftDeletingModelInterface.php). This ensures a higher level of compatibility and code integrity.

### Table of Contents

- [Validating Model Trait](#validating-model-trait)
    - [Auto-Validating on Save](#auto-validating-on-save)
    - Manually Validating Models
    - Handling Validation Errors
    - Using Force Save
- Purging Model Trait
    - Auto-Purging on Save
    - Using the Purgeable Property
- Hashing Model Trait
    - Auto-Hashing on Save
    - Using the Hashable Property
    - Checking Hash Value
- Encrypting Model Trait
    - Auto-Encrypting on Set
    - Auto-Decrypting on Get
    - Using the Encryptable Property
    - Checking Encryption State
- Soft Deleting Model Trait
    - Using Soft Deletes
    - Adding Custom Dates
    - Using Force Delete
- Relating Model Trait
    - Using Simplified Relationships
- Unit Tests
    - Running the Unit Tests
- Credits
    - Contributing
    - MIT License

### Validating Model Trait

This package includes the [`ValidatingModelTrait`](https://github.com/esensi/model/blob/master/src/Traits/ValidatingModelTrait.php) which implements the [`ValidatingModelInterface`](https://github.com/esensi/model/blob/master/src/Contracts/ValidatingModelInterface.php) on any `Eloquent` model that uses it. The `ValidatingModelTrait` adds methods to `Eloquent` models for:

- Automatic self-validation of models on `create()`, `update()`, `save()`, `delete()`, and `restore()` method calls
- Integration with Laravel's `Validation` facade to validate model attributes according to sets of rules and return a `MessageBag` of errors when it fails
- Choice of throwing `ValidationException` when attempting to save an invalid model or simply return `false` without actually saving
- Ability to `forceSave()` a model to bypass any validation rules that would other wise prevent a model from saving
- Automatic injection (or not, if you rather) of the model's identifier for `unique` validation rules

Like all the traits it is self-contained and can be used individually. Special credit goes to the very talented [Dwight Watson](https://github.com/dwightwatson) and his [Watson/Validating Laravel package](https://github.com/dwightwatson/validating) which is the basis for this trait. Emerson Media collaborated with him as he created the package. We wrap his traits with our own and you should review his package in detail to see the inner workings.

#### Auto-Validating On Save

While you can of course use the [`Model`](https://github.com/esensi/model/blob/master/src/Model.php) or [`SoftModel`](https://github.com/esensi/model/blob/master/src/SoftModel.php) classes which already include the [`ValidatingModelTrait`](https://github.com/esensi/model/blob/master/src/Traits/ValidatingModelTrait.php), the following code will demonstrate adding auto-validation to any `Eloquent` based model.

```php
<?php

use \Esensi\Model\Contracts\ValidatingModelInterface;
use \Esensi\Model\Traits\ValidatingModelTrait;
use \Illuminate\Database\Eloquent\Model as Eloquent;

class Post extends Eloquent implements ValidatingModelInterface {

    use ValidatingModelTrait;

    /**
     * This tells whether or not the model should inject its identifier
     * into the unique validation rules before attempting validation.
     *
     * @var boolean
     */
    protected $injectUniqueIdentifier = true;

    /**
     * These are the default rules that the model will validate against.
     * You will probably want to specify generic validation rules
     * that would apply in any save operation vs. form or route
     * specific validation rules.
     *
     * @var array
     */
    protected $rules = [
       'title' => [ 'max:64' ],
       'slug' => [ 'max:16', 'alpha_dash', 'unique' ],
       'published' => [ 'boolean' ],
       // ... more attribute rules
    ];

    /**
     * These are the rulesets that the model will validate against
     * during specific save operations. Rulesets should be keyed
     * by either the in progress event name of the save operation
     * or a custom unique key for custom validation.
     *
     * The following rulesets are automatically applied during
     * corresponding save operations:
     *     "creating" after "saving" but before save() is called (on new models)
     *     "updating" after "saving" but before save() is called (on existing models)
     *     "saving" before save() is called (and only if no "creating" or "updating")
     *     "deleting" when calling delete() method
     *     "restoring" when calling restore() method (on a soft-deleting model)
     *
     * @var array
     */
    protected $rulesets = [
       
        'creating' => [
            'title' => [ 'required', 'max:64' ],
            'slug' => [ 'required', 'alpha_dash', 'max:16', 'unique' ],
            'published' => [ 'boolean' ],
            // ... more attribute rules to validate against when creating
        ],

        'updating' => [
            'title' => [ 'required', 'max:64' ],
            'slug' => [ 'required', 'alpha_dash', 'max:16', 'unique' ],
            'published' => [ 'boolean' ],
            // ... more attribute rules to validate against when updating
        ],
    ];

}
```

Then from your controller or repository you can interact with the `Post` model's attributes, call the `save()` method and let the `Post` model handle validation automatically. For demonstrative purposes the following code shows this pattern from a simple route closure:

```php
Route::post( 'posts', function()
{
    // Hydrate the model from the Input
    $attributes = Input::only( 'title', 'slug', 'published' );
    $post = new Post( $attributes );

    // Attempt to save, will return false on invalid model.
    // Because this is a new model, the "creating" ruleset will
    // be used to validate against. If it does not exist then the
    // "saving" ruleset will be attempted. If that does not exist, then
    // finally it will default to the Post::$rules.
    if ( ! $post->save() )
    {
        // Redirect back to the form with the message bag of errors
        return Redirect::to( 'posts' )
            ->withErrors( $post->getErrors() )
            ->withInput();
    }

    // Redirect to the new post
    return Redirect::to( 'posts/' . $post->id );
});
```

Calling the `save()` method on the newly created `Post` model would instead use the "updating" ruleset from `Post::$ruleset` while saving. If that ruleset did not exist then it would default to using the `Post::$rules`.
