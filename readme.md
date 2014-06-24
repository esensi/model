## Esensi Model Traits Package

An [Esensi](https://github.com/esensi) package, coded by [Emerson Media](http://www.emersonmedia.com).

> **Want to work with us on great Laravel applications?**
Email us at [careers@emersonmedia.com](http://emersonmedia.com/contact)

The `Esensi/Model` package is just one package that makes up [Esensi](https://github.com/esensi), a platform built on [Laravel](http://laravel.com). This package uses [PHP traits](http://php.net/traits) to extend Laravel's default Eloquent models and traits. Using traits allows for a high-degree of code reusability and extensibility. While this package provides some reasonable base models, developers are free to mix and match traits in any combination needed, being confident that the code complies to a reliable interface and is properly unit tested. For more details on the inner workings of the traits please consult the generously documented source code.

> **Have a project in mind?**
_Email us at [sales@emersonmedia.com](http://emersonmedia.com/contact), or call 1.877.439.6665._

## Quick Start

> **Notice:** _This code is specifically designed to be compatible with the [Laravel Framework](http://laravel.com) and may not be compatible as a stand-alone dependency or as part of another framework._

### Add the Package to Composer

Add the `esensi/model` package as a dependency to the application. Using [Composer](https://getcomposer.org), this can be done from the command line:

```bash
composer require esensi/modal 0.3.*
```

Or manually it can be added to the `composer.json` file:

```json
{
    "require": {
        "esensi/model": "0.3.*"
    }
}
```

If manually adding the package, then be sure to run `composer update` to update the dependencies.

### Extend the Default Model

The simplest way to demonstrate the traits is to extend the base [`Esensi\Model\Model`](https://github.com/esensi/model/blob/master/src/Model.php). For example, if the application requires a simple blog, then the developer could create a `Post` model that automatically handles validation, purging, hashing, encrypting, and even simplified relationship bindings by simply extending this ready-to-go model:

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

> **Pro Tip:** Take a look at the generously commented [`Esensi\Model\Model`](https://github.com/esensi/model/blob/master/src/Model.php) source code for details on how to use individual traits without extending the default model.

### Use Soft Deletes Instead

If the application requires that the articles be sent to the trash before permanently deleting them, then the developer can just swap out the [`Esensi\Model\Model`](https://github.com/esensi/model/blob/master/src/Model.php) with the soft deleting version [`Esensi\Model\SoftModel`](https://github.com/esensi/model/blob/master/src/SoftModel.php) like so:

```php
<?php

use \Esensi\Model\SoftModel;

class Post extends SoftModel {

}
```

> **Pro Tip:** While Laravel includes `SoftDeletingTrait`, Esensi expands upon this by also forcing the trait to comply with a [`SoftDeletingModelInterface`](https://github.com/esensi/model/blob/0.3/src/Contracts/SoftDeletingModelInterface.php) contract. This ensures a higher level of compatibility and code integrity.

## Table of Contents

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
- [Contributing](#contributing)
- [Licensing](#licensing)

## Validating Model Trait

This package includes the [`ValidatingModelTrait`](https://github.com/esensi/model/blob/master/src/Traits/ValidatingModelTrait.php) which implements the [`ValidatingModelInterface`](https://github.com/esensi/model/blob/master/src/Contracts/ValidatingModelInterface.php) on any `Eloquent` model that uses it. The `ValidatingModelTrait` adds methods to `Eloquent` models for:

- Automatic validation of models on `create()`, `update()`, `save()`, `delete()`, and `restore()` method calls
- Integration with Laravel's `Validation` facade to validate model attributes according to sets of rules
- Integration with Laravel's `MessageBag` so that models can return errors when validation fails
- Option to throw `ValidationException` when validation fails
- Ability to `forceSave()` a model and bypass validation rules that would other wise prevent a model from saving
- Automatic injection (or not) of the model's identifier for `unique` validation rules

Like all the traits it is self-contained and can be used individually. Special credit goes to the very talented [Dwight Watson](https://github.com/dwightwatson) and his [Watson/Validating Laravel package](https://github.com/dwightwatson/validating) which is the basis for this trait. Emerson Media collaborated with him as he created the package. Esensi wraps his traits with consistent naming conventions for the other Esensi model traits. Please review his package in detail to see the inner workings.

### Auto-Validating On Save

While developers can of course use the [`Model`](https://github.com/esensi/model/blob/master/src/Model.php) or [`SoftModel`](https://github.com/esensi/model/blob/master/src/SoftModel.php) classes which already include the [`ValidatingModelTrait`](https://github.com/esensi/model/blob/master/src/Traits/ValidatingModelTrait.php), the following code will demonstrate adding auto-validation to any `Eloquent` based model.

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
     * Developers will probably want to specify generic validation rules
     * that would apply in any save operation vs. form or route
     * specific validation rules. For simple models, these rules can
     * apply to all save operations.
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
     *
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

Then from the controller or repository the developer can interact with the `Post` model's attributes, call the `save()` method and let the `Post` model handle validation automatically. For demonstrative purposes the following code shows this pattern from a simple route closure:

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

## Relating Model Trait

This package includes the [`RelatingModelTrait`](https://github.com/esensi/model/blob/master/src/Traits/RelatingModelTrait.php) which implements the [`RelatingModelInterface`](https://github.com/esensi/model/blob/master/src/Contracts/RelatingModelInterface.php) on any `Eloquent` model that uses it. The `RelatingModelTrait` adds methods to `Eloquent` models for automatically resolving related models:

- from simplified configs
- as magic method calls
- as magic attribute calls

Like all the traits it is self-contained and can be used individually. Using this trait does require a few changes to the actual model which makes this trait's use a bit unique and is better used on a base model like [Esensi\Model\Model](https://github.com/esensi/model/blob/master/src/Model.php). Special credit goes to [Phillip Brown](https://github.com/phillipbrown) and his [Philipbrown/Magniloquent Laravel package](https://github.com/philipbrown/magniloquent) which contained was the inspiration for this trait.

### Using Simplified Relationships

While developers can of course use the [`Model`](https://github.com/esensi/model/blob/master/src/Model.php) or [`SoftModel`](https://github.com/esensi/model/blob/master/src/SoftModel.php) classes which already include the [`RelatingModelTrait`](https://github.com/esensi/model/blob/master/src/Traits/RelatingModelTrait.php), the following code will demonstrate adding simplified relationship bindings to any `Eloquent` based model.

```php
<?php

use \Esensi\Model\Contracts\RelatingModelInterface;
use \Esensi\Model\Traits\RelatingModelTrait;
use \Illuminate\Database\Eloquent\Model as Eloquent;

class Post extends Eloquent implements RelatingModelInterface {

    use RelatingModelTrait;

    /**
     * These are the relationships that the model should set up.
     * Using PHP and Laravel's magic methods, these relationship
     * keys resolve to the actual models automatically.
     *
     * @example relationship bindings:
     *
     *     [ 'hasOne', 'related', 'foreignKey', 'localKey' ]
     *     [ 'hasMany', 'related', 'foreignKey', 'localKey' ]
     *     [ 'hasManyThrough', 'related', 'through', 'firstKey', 'secondKey' ]
     *     [ 'belongsTo', 'related', 'foreignKey', 'otherKey', 'relation' ]
     *     [ 'belongsToMany', 'related', 'foreignKey', 'otherKey', 'relation' ]
     *     [ 'morphOne', 'related', 'name', 'type', 'id', 'localKey' ]
     *     [ 'morphTo', 'name', 'type', 'id' ]
     *     [ 'morphMany', 'related', 'name', 'type', 'id', 'localKey' ]
     *     [ 'morphToMany', 'related', 'name', 'table', 'foreignKey', 'otherKey', 'inverse' ]
     *     [ 'morphByMany', 'related', 'name', 'table', 'foreignKey', 'otherKey' ]
     *
     * @var array
     */
    protected $relationships = [
        
        // Bind Comment model as a hasMany relationship.
        // Use Post::comments() to query the relationship.
        'comments' => [ 'hasMany', 'Comment' ],
        
        // Bind User model as a belongsTo relationship.
        // Use $post->author to get the User model.
        'author' => [ 'belongsTo', 'User' ]
    ];

    /**
     * Dynamically call relationship models.
     *
     * @param  string $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call( $method, $parameters )
    {
        // Dynamically call the relationship
        if ( $this->isRelationship( $method ) )
        {
            return $this->callRelationship( $method );
        }

        // Default Eloquent dynamic caller
        return parent::__call($method, $parameters);
    }

    /**
     * Dynamically get relationship models.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get( $key )
    {
        // Dynamically get the relationship
        if ( $this->isRelationship( $key ) )
        {
            // Use the relationship already loaded
            if ( array_key_exists( $key, $this->getRelations() ) )
            {
                return $this->getRelation( $key );
            }

            return $this->getRelationshipFromMethod($key, camel_case($key));
        }

        // Default Eloquent dynamic getter
        return parent::__get( $key );
    }

}
```

The developer can now use the `Post` model's relationships from a controller or repository and the trait will automatically resolve the relationship bindings. For demonstrative purposes the following code shows this pattern from a simple route closure:

```php
Route::get( 'posts/{id}/comments', function( $id )
{
    // Retrieve the post by ID
    $post = Post::find( $id );

    // Query the post for all the related comments.
    // The trait will resolve the "comments" from
    // the Post::$relationships bindings.
    $comments = $post->comments()->all();

    // It is also possible to shorten this using the
    // magic attributes instead. It is equivalent to
    // the above call.
    $comments = $post->comments;

    // ... pass the $comments collection to the view
});
```

## Contributing

> **Want to work with us on great Laravel applications?**
Email us at [careers@emersonmedia.com](http://emersonmedia.com/contact)

[Emerson Media](http://www.emersonmedia.com) is proud to work with some of the most talented developers in the PHP community. The developer team welcomes requests, suggestions, issues, and of course pull requests. When submitting issues please be as detailed as possible and provide code examples where possible. When submitting pull requests please follow the same code formatting and style guides that the Esensi code base uses. Please help the open-source community out by including good code test coverage with your pull requests. **All pull requests must be submitted to the version branch to which the code changes apply.**

## Licensing

> **Have a project in mind?**
_Email us at [sales@emersonmedia.com](http://emersonmedia.com/contact), or call 1.877.439.6665._

Copyright (c) 2014 [Emerson Media, LP](http://www.emersonmedia.com)

This package is released under the MIT license. Please see the [LICENSE.txt](https://github.com/esensi/model/blob/master/LICENSE.txt) file distributed with every copy of the code for commercial licensing terms.
