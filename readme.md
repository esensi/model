## Esensi Model Traits Package

[![Build Status](https://travis-ci.org/esensi/model.svg)](https://travis-ci.org/esensi/model)
[![Total Downloads](https://poser.pugx.org/esensi/model/downloads.svg)](https://packagist.org/packages/esensi/model)
[![Latest Stable Version](https://poser.pugx.org/esensi/model/v/stable.svg)](https://github.com/esensi/model/releases)
[![License](https://poser.pugx.org/esensi/model/license.svg)](https://github.com/esensi/model#licensing)

An [Esensi](https://github.com/esensi) package, coded by [Emerson Media](http://www.emersonmedia.com).

> **Want to work with us on great Laravel applications?**
Email us at [careers@emersonmedia.com](http://emersonmedia.com/contact)

The `Esensi/Model` package is just one package that makes up [Esensi](https://github.com/esensi), a platform built on [Laravel](http://laravel.com). This package uses [PHP traits](http://culttt.com/2014/06/25/php-traits) to extend Laravel's default Eloquent models and traits. Using traits allows for a high-degree of code reusability and extensibility. While this package provides some reasonable base models, developers are free to mix and match traits in any combination needed, being confident that the code complies to a reliable interface and is properly unit tested. For more details on the inner workings of the traits please consult the generously documented source code.

> **Have a project in mind?**
Email us at [sales@emersonmedia.com](http://emersonmedia.com/contact), or call 1.877.439.6665.

## Quick Start

> **Notice:** This code is specifically designed to be compatible with the [Laravel Framework](http://laravel.com) and may not be compatible as a stand-alone dependency or as part of another framework.

### Extend the Default Model

The simplest way to demonstrate the traits is to extend the base [`Esensi\Model\Model`](https://github.com/esensi/model/blob/master/src/Model.php). For example, if the application requires a simple blog, then the developer could create a `Post` model that **automatically handles validation, purging, hashing, encrypting, attribute type juggling and even simplified relationship bindings** by simply extending this ready-to-go model:

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

> **Pro Tip:** Take a look at the generously commented [`Esensi\Model\Model`](https://github.com/esensi/model/blob/master/src/Model.php) source code for details on how to use individual traits with and without extending the default model.

### Use Soft Deletes Instead

If the application requires that the articles be sent to the trash before permanently deleting them, then the developer can just swap out the [`Esensi\Model\Model`](https://github.com/esensi/model/blob/master/src/Model.php) with the soft deleting version [`Esensi\Model\SoftModel`](https://github.com/esensi/model/blob/master/src/SoftModel.php) like so:

```php
<?php

use \Esensi\Model\SoftModel;

class Post extends SoftModel {

}
```

> **Pro Tip:** While Laravel includes `SoftDeletingTrait`, Esensi expands upon this by also forcing the trait to comply with a [`SoftDeletingModelInterface`](https://github.com/esensi/model/blob/0.5/src/Contracts/SoftDeletingModelInterface.php) contract. This ensures a higher level of compatibility and code integrity.

## Table of Contents

> **Help Write Better Documentation:** The documentation is still a work in progress. You can help others learn to reuse code by contributing better documentation as a pull request.

- **[Installation](#installation)**
- **[Validating Model Trait](#validating-model-trait)**
    - [Auto-Validating on Save](#auto-validating-on-save)
    - Manually Validating Model Attributes
    - Handling Validation Errors
    - Using Force Save
- **[Purging Model Trait](#purging-model-trait)**
    - [Auto-Purging on Save](#auto-purging-on-save)
    - [Manually Purging Model Attributes](#manually-purging-model-attributes)
- **[Hashing Model Trait](#hashing-model-trait)**
    - [Auto-Hashing on Save](#auto-hashing-on-save)
    - [Manually Hashing Model Attributes](#manually-hashing-model-attributes)
- **[Encrypting Model Trait](#encrypting-model-trait)**
    - Auto-Encrypting on Access
    - [Manually Encrypting Model Attributes](#manually-encrypting-model-attributes)
    - Checking Encryption State
- **[Juggling Model Trait](#juggling-model-trait)**
    - [Auto-Juggling on Access](#auto-juggling-on-access)
    - [Manually Juggling Model Attributes](#manually-juggling-model-attributes)
- **[Soft Deleting Model Trait](#soft-deleting-model-trait)**
    - Using Soft Deletes
    - Adding Custom Dates
    - Using Force Delete
- **[Relating Model Trait](#relating-model-trait)**
    - [Using Simplified Relationships](#using-simplified-relationships)
- **Sluggable Model Trait**
    - Using Slugs on Models
- **[Unit Testing](#unit-testing)**
    - [Running the Unit Tests](#running-the-unit-tests)
- **[Contributing](#contributing)**
- **[Licensing](#licensing)**

## Installation

Add the `esensi/model` package as a dependency to the application. Using [Composer](https://getcomposer.org), this can be done from the command line:

```bash
composer require esensi/model 0.5.*
```

Or manually it can be added to the `composer.json` file:

```json
{
    "require": {
        "esensi/model": "0.5.*"
    }
}
```

If manually adding the package, then be sure to run `composer update` to update the dependencies.

## Validating Model Trait

This package includes the [`ValidatingModelTrait`](https://github.com/esensi/model/blob/master/src/Traits/ValidatingModelTrait.php) which implements the [`ValidatingModelInterface`](https://github.com/esensi/model/blob/master/src/Contracts/ValidatingModelInterface.php) on any `Eloquent` model that uses it. The `ValidatingModelTrait` adds methods to `Eloquent` models for:

- Automatic validation of models on `create()`, `update()`, `save()`, `delete()`, and `restore()` method calls
- Integration with Laravel's `Validation` facade to validate model attributes according to sets of rules
- Integration with Laravel's `MessageBag` so that models can return errors when validation fails
- Option to throw `ValidationException` when validation fails
- Ability to `forceSave()` and bypass validation rules that would other wise prevent a model from saving
- Automatic injection (or not) of the model's identifier for `unique` validation rules

Like all the traits, it is self-contained and can be used individually. Special credit goes to the very talented [Dwight Watson](https://github.com/dwightwatson) and his [Watson/Validating Laravel package](https://github.com/dwightwatson/validating) which is the basis for this trait. Emerson Media collaborated with him as he created the package. Esensi wraps his traits with consistent naming conventions for the other Esensi model traits. Please review his package in detail to see the inner workings.

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

## Purging Model Trait

This package includes the [`PurgingModelTrait`](https://github.com/esensi/model/blob/master/src/Traits/PurgingModelTrait.php) which implements the [`PurgingModelInterface`](https://github.com/esensi/model/blob/master/src/Contracts/PurgingModelInterface.php) on any `Eloquent` model that uses it. The `PurgingModelTrait` adds methods to `Eloquent` models for automatically purging attributes from the model just before write operations to the database. The trait automatically purges:

- attributes in the `$purgeable` property
- attributes prefixed with an underscore (i.e.: `_private`)
- attributes ending in `_confirmation` (i.e.: `password_confirmation`)

Like all the traits, it is self-contained and can be used individually.

> **Pro Tip:** This trait uses the `PurgingModelObserver` to listen for the `eloquent.creating` and `eloquent.updating` events before automatically purging the purgeable attributes. The order in which the traits are used in the `Model` determines the event priority: if using the `ValidatingModelTrait` be sure to use it first so that the purging event listner is fired _after_ the validating event listener has fired.

### Auto-Purging on Save

While developers can of course use the [`Model`](https://github.com/esensi/model/blob/master/src/Model.php) or [`SoftModel`](https://github.com/esensi/model/blob/master/src/SoftModel.php) classes which already include the [`PurgingModelTrait`](https://github.com/esensi/model/blob/master/src/Traits/PurgingModelTrait.php), the following code will demonstrate using automatic purging on any `Eloquent` based model.

```php
<?php

use \Esensi\Model\Contracts\PurgingModelInterface;
use \Esensi\Model\Traits\PurgingModelTrait;
use \Illuminate\Database\Eloquent\Model as Eloquent;

class Post extends Eloquent implements PurgingModelInterface {

    use PurgingModelTrait;

    /**
     * These are the attributes to purge before saving.
     *
     * Remember, anything prefixed with "_" or ending
     * in "_confirmation" will automatically be purged
     * and does not need to be listed here.
     *
     * @var array
     */
    protected $purgeable = [
        'analytics_id',
        '_private_attribute',
        'password_confirmation',
    ];

}
```

> **Pro Tip:** From an efficiency stand point, it is theoretically better to assign all purgeable attributes in the `$purgeable` property including underscore prefixed and `_confirmation` suffixed attributes since the `$purgeable` property is checked first and does not require string parsing and comparisons.

The developer can now pass form input to the `Post` model from a controller or repository and the trait will automatically purge the non-attributes before saving. This gets around those pesky "Unknown column" MySQL errors. For demonstrative purposes the following code shows this in practice from a simple route closure:

```php
Route::post( 'posts', function( $id )
{
    // Hydrate the model from the Input
    $input = Input::all();
    $post = new Post($input);

    // At this point $post->analytics_id might exist.
    // If we tried to save it, MySQL would throw an error.

    // Save the Post
    $post->save();

    // At this point $post->analytics_id is for sure purged.
    // It was filtered becaused it existed in Post::$purgeable.
});
```

### Manually Purging Model Attributes

It is also possible to manually purge attributes. The `PurgingModelTrait` includes several helper functions to make manual manipulation of the `$purgeable` property easier.

```php
// Hydrate the model from the Input
$post = Post::find($id);
$post->fill( Input::all() );

// Manually purge attributes prior to save()
$post->purgeAttributes();

// Manually get the attributes
$post->getHashable(); // ['foo']

// Manually set the purgeable attributes
$post->setPurgeable( ['foo', 'bar'] ); // ['foo', 'bar']

// Manually add an attribute to the purgeable attributes
$post->addPurgeable( 'baz' ); // ['foo', 'bar', 'baz']
$post->mergePurgeable( ['zip'] ); // ['foo', 'bar', 'baz', 'zip']
$post->removePurgeable( 'foo' ); // ['bar', 'baz', 'zip']

// Check if an attribute is in the Post::$purgeable property
if ( $post->isPurgeable( 'foo' ) )
{
    // ... foo is not purgeable so this would not get executed
}

// Do not run purging for this save only.
// This is useful when purging is enabled
// but needs to be temporarily bypassed.
$post->saveWithoutPurging();

// Disable purging
$post->setPurging(false); // a value of true would enable it

// Run purging for this save only.
// This is useful when purging is disabled
// but needs to be temporarily ran while saving.
$post->saveWithPurging();
```

## Hashing Model Trait

This package includes the [`HashingModelTrait`](https://github.com/esensi/model/blob/master/src/Traits/HashingModelTrait.php) which implements the [`HashingModelInterface`](https://github.com/esensi/model/blob/master/src/Contracts/HashingModelInterface.php) on any `Eloquent` model that uses it. The `HashingModelTrait` adds methods to `Eloquent` models for automatically hashing attributes on the model just before write operations to the database. The trait includes the ability to:

- automatically hash attributes in the `$hashable` property
- manually hash a value using the `hash()` method
- compare a plain text value with a hash using the `checkHash()` method
- check if a value is hashed using the `isHashed()` method
- swap out the `HasherInterface` used using the `setHasher()` method

Like all the traits, it is self-contained and can be used individually.

> **Pro Tip:** This trait uses the `HashingModelObserver` to listen for the `eloquent.creating` and `eloquent.updating` events before automatically hashing the hashable attributes. The order in which the traits are used in the `Model` determines the event priority: if using the `ValidatingModelTrait` be sure to use it first so that the hashing event listner is fired _after_ the validating event listener has fired.

### Auto-Hashing on Save

While developers can of course use the [`Model`](https://github.com/esensi/model/blob/master/src/Model.php) or [`SoftModel`](https://github.com/esensi/model/blob/master/src/SoftModel.php) classes which already include the [`HashingModelTrait`](https://github.com/esensi/model/blob/master/src/Traits/HashingModelTrait.php), the following code will demonstrate using automatic hashing on any `Eloquent` based model. For this example, the implementation of automatic hashing will be applied to a `User` model which requires the password to be hashed on save:

```php
<?php

use \Esensi\Model\Contracts\HashingModelInterface;
use \Esensi\Model\Traits\HashingModelTrait;
use \Illuminate\Database\Eloquent\Model as Eloquent;

class User extends Eloquent implements HashingModelInterface {

    use HashingModelTrait;

    /**
     * These are the attributes to hash before saving.
     *
     * @var array
     */
    protected $hashable = [ 'password' ];

}
```

> **Pro Tip:** The `HashingModelTrait` is a great combination for the `PurgingModelTrait`. Often hashable attributes need to be confirmed and using the `PurgingModelTrait`, the model can be automatically purged of the annoying `_confirmation` attributes before writing to the database. While the `use` order of these two traits is not important relative to each other, it is important to `use` them after `ValidatingModelTrait` if that trait is used as well. Otherwise, the model will purge or hash the attributes before validating.

The developer can now pass form input to the `User` model from a controller or repository and the trait will automatically hash the `password` before saving. For demonstrative purposes the following code shows this in practice from a simple route closure:

```php
Route::post( 'account', function()
{
    // Hydrate the model from the Input
    $user = Auth::user();
    $user->password = Input::get('password');

    // At this point $user->password is still plain text.
    // This allows for the value to be checked by validation.

    // Save the User
    $user->save();

    // At this point $user->password is for sure hashed.
    // It was hashed becaused it existed in User::$hashable.
});
```

### Manually Hashing Model Attributes

It is also possible to manually hash attributes. The `HashingModelTrait` includes several helper functions to make manual manipulation of the `$hashable` property easier.

```php
// Hydrate the model from the Input
$post = User::find($id);
$post->password = Input::get('password');

// Manually hash attributes prior to save()
$post->hashAttributes();

// Manually get the attributes
$post->getHashable(); // ['foo']

// Manually set the hashable attributes
$post->setHashable( ['foo', 'bar'] ); // ['foo', 'bar']

// Manually add an attribute to the hashable attributes
$post->addHashable( 'baz' ); // ['foo', 'bar', 'baz']
$post->mergeHashable( ['zip'] ); // ['foo', 'bar', 'baz', 'zip']
$post->removeHashable( 'foo' ); // ['bar', 'baz', 'zip']

// Check if an attribute is in the User::$hashable property
if ( $post->isHashable( 'foo' ) )
{
    // ... foo is not hashable so this would not get executed
}

// Check if an attribute is already hashed
if ( $post->isHashed( 'foo' ) )
{
    // ... if foo were hashed this would get executed
}

// Check if the password when hashed matches the stored password.
// This is just a unified shorthand to Crypt::checkHash().
if ( $post->checkHash( 'password123', $post->password ) )
{
    // ... if the password matches you could authenticate the user
}

// Swap out the HasherInterface used
$post->setHasher( new MyHasher() );

// Do not run hashing for this save only.
// This is useful when hashing is enabled
// but needs to be temporarily bypassed.
$post->saveWithoutHashing();

// Disable hashing
$post->setHashing(false); // a value of true would enable it

// Run hashing for this save only.
// This is useful when hashing is disabled
// but needs to be temporarily ran while saving.
$post->saveWithHashing();
```

## Encrypting Model Trait

This package includes the [`EncryptingModelTrait`](https://github.com/esensi/model/blob/master/src/Traits/EncryptingModelTrait.php) which implements the [`EncryptingModelInterface`](https://github.com/esensi/model/blob/master/src/Contracts/EncryptingModelInterface.php) on any `Eloquent` model that uses it. The `EncryptingModelTrait` adds methods to `Eloquent` models for automatically encrypting attributes on the model whenever they are set and for automatically decrypting attributes on the model whenever they are got. The trait includes the ability to:

- automatically encrypt attributes in the `$encryptable` property when setting them
- automatically decrypt attributes in the `$encryptable` property when getting them
- manually encrypt/decrypt a value using the `encrypt()` and `decrypt()` methods
- check if a value is encrypted using the `isEncrypted()` method
- swap out the encrypter class used using the `setEncrypter()` method

Like all the traits, it is self-contained and can be used individually. Be aware, however, that using this trait does overload the magic `__get()` and `__set()` methods of the model (see [Esensi\Model\Model](https://github.com/esensi/model/blob/master/src/Model.php) source code for how to deal with overloading conflicts).

### Manually Encrypting Model Attributes

It is also possible to manually encrypt attributes. The `EncryptingModelTrait` includes several helper functions to make manual manipulation of the `$encryptable` property easier.

```php
// Hydrate the model from the Input
$post = Model::find($id);
$post->secret = Input::get('secret'); // automatically encrypted

// Manually encrypt attributes prior to save()
$post->encryptAttributes();

// Manually encrypt and decrypte a value
$encrypted = $post->encrypt( 'plain text' );
$decrypted = $post->decrypt( $encrypted ); // plain text

// Manually get the attributes
$post->getEncryptable(); // ['foo']

// Manually set the encryptable attributes
$post->setEncryptable( ['foo', 'bar'] ); // ['foo', 'bar']

// Manually add an attribute to the encryptable attributes
$post->addEncryptable( 'baz' ); // ['foo', 'bar', 'baz']
$post->mergeEncryptable( ['zip'] ); // ['foo', 'bar', 'baz', 'zip']
$post->removeEncryptable( 'foo' ); // ['bar', 'baz', 'zip']

// Check if an attribute is in the Model::$encryptable property
if ( $post->isEncryptable( 'foo' ) )
{
    // ... foo is not encryptable so this would not get executed
}

// Check if an attribute is already encrypted.
// You could also check $post->isDecrypted( 'foo' ).
if ( $post->isEncrypted( 'foo' ) )
{
    // ... if foo were encrypted this would get executed
}

// Swap out the encrypter class used
$post->setEncrypter( new MyEncrypter() );

// Disable encrypting
$post->setEncrypting(false); // a value of true would enable it
```

## Juggling Model Trait

This package includes the [`JugglingModelTrait`](https://github.com/esensi/model/blob/master/src/Traits/JugglingModelTrait.php) which implements the [`JugglingModelInterface`](https://github.com/esensi/model/blob/master/src/Contracts/JugglingModelInterface.php) on any `Eloquent` model that uses it. The `JugglingModelTrait` adds methods to `Eloquent` models for automatically type casting (juggling) attributes on the model whenever they are got or set. The trait includes the ability to:

- automatically cast attributes to a type when getting them
- automatically cast attributes to a type when setting them
- manually casting a value using the `juggle()` method
- manually casting to pre-defined types including:
    - `string` => `juggleString()`
    - `boolean` (`bool`) => `juggleBoolean()`
    - `integer` (`integer`) => `juggleInteger()`
    - `float` (`double`) => `juggleFloat()`
    - `array` => `juggleArray()`
    - `date` => `juggleDate()` (returns Carbon date)
    - `dateTime` (`datetime` or `date_time`) => `juggleDateTime()` (returns 0000-00-00 00:00:00 format)
    - `timestamp` => `juggleTimestamp()` (returns Unix timestamp)
- create custom types to cast to with magic methods like:
    - Example: `fooBar` => `juggleFooBar()`

Like all the traits, it is self-contained and can be used individually. Be aware, however, that using this trait does overload the magic `__get()` and `__set()` methods of the model (see [Esensi\Model\Model](https://github.com/esensi/model/blob/master/src/Model.php) source code for how to deal with overloading conflicts). Special credit goes to the brilliant [Dayle Rees](https://github.com/daylerees), author of [Code Bright book](https://leanpub.com/codebright), who inspired this trait with his [pull request to Laravel](https://github.com/laravel/framework/pull/4948).

### Auto-Juggling on Access

> **Pro Tip:** PHP extensions like `php-mysqlnd` should be used when available to handle casting from and to persistent storage, this trait serves a dual purpose of type casting and simplified attribute mutation (juggling) especially when a native extension is not available.

While developers can of course use the [`Model`](https://github.com/esensi/model/blob/master/src/Model.php) or [`SoftModel`](https://github.com/esensi/model/blob/master/src/SoftModel.php) classes which already include the [`JugglingModelTrait`](https://github.com/esensi/model/blob/master/src/Traits/JugglingModelTrait.php), the following code will demonstrate using automatic type juggling on any `Eloquent` based model. For this example, the implementation of automatic type juggling will be applied to a `Post` model which requires certain attributes to be type casted when attributes are accessed:

```php
<?php

use \Esensi\Model\Contracts\JugglingModelInterface;
use \Esensi\Model\Traits\JugglingModelTrait;
use \Illuminate\Database\Eloquent\Model as Eloquent;

class Post extends Eloquent implements JugglingModelInterface {

    use JugglingModelTrait;

    /**
     * Attributes to cast to a different type.
     *
     * @var array
     */
    protected $jugglable = [

        // Cast the published_at attribute to a date
        'published_at' => 'date',

        // Cast the terms attribute to a boolean
        'terms'        => 'boolean',

        // Cast the foo attribute to a custom bar type
        'foo'          => 'bar',
    ];

    /**
     * Example of a custom juggle "bar" type.
     *
     * @param  mixed $value
     * @return string
     */
    protected function juggleBar( $value )
    {
        return 'bar';
    }

}
```

The developer can now pass form input to the `Post` model from a controller or repository and the trait will automatically type cast/juggle the attributes when setting. The same holds true for when the attributes are loaded from persistent storage as the model is constructed: the attributes are juggled to their types. Even for persistent storage that does not comply, the jugglable attributes are automatically type casted when retrieved from the model. For demonstrative purposes the following code shows this in practice from a simple route closure:

```php
Route::post( 'post/{id}/publish', function( $id )
{
    // Hydrate the model from the Input
    $post = Post::find($id);

    // published_at will be converted to a Carbon date object.
    // You could then do $post->published_at->format('Y-m-d').
    $post->published_at = Input::get('published_at');

    // Convert those pesky checkboxes to proper boolean.
    $post->terms = Input::get('terms', false);

    // foo attribute will be casted as the custom "bar" type
    // using the method juggleBar: so it's value would now be "bar".
    $post->foo = Input::get('bar');

    // Save the Post or do something else
    $post->save();
});
```

> **Pro Tip:** Some great uses for `JugglingModelTrait` would be custom "types" that map to commonly mutators jugglers for `phone`, `url`, `json`, types etc. Normally developers would have to map the attributes to attribute mutators and accessors which are hard-coded to the attribute name. Using the `$jugglable` property these attributes can be mapped to custom juggle methods easily in a reusable way.


### Manually Juggling Model Attributes

It is also possible to manually juggle attributes. The `JugglingModelTrait` includes several helper functions to make manual manipulation of the `$jugglable` property easier.

```php
// Hydrate the model from the Input
$post = Model::find($id);
$post->foo = Input::get('foo'); // automatically juggled

// Manually juggle attributes after setting
$post->juggleAttributes();

// Manually juggle a value to a type
$boolean = $post->juggle( 'true', 'boolean' ); // bool(true)
$boolean = $post->juggleBoolean( '0' ); // bool(false)
$array = $post->juggleArray( 'foo' ); // array(0 => foo)
$date = $post->juggleDate( '2014-07-10' ); // object(\Carbon\Carbon)
$dateTime = $post->juggleDateTime( Carbon::now() ); // string(2014-07-10 11:17:00)
$timestamp = $post->juggleTimestamp( '07/10/2014 11:17pm' ); // integer(1405034225)

// Manually get the attributes
$post->getJugglable(); // ['foo' => 'string']

// Manually set the jugglable attributes
$post->setJugglable( ['bar' => 'boolean'] ); // ['bar' => 'boolean']

// Manually add an attribute to the jugglable attributes
$post->addJugglable( 'baz', 'integer' ); // ['bar' => 'boolean', 'baz' => 'integer']
$post->mergeJugglable( ['zip' => 'array'] ); // ['bar' => 'boolean', 'baz' => 'integer', 'zip' => 'array']
$post->removeJugglable( 'bar' ); // ['baz' => 'integer', 'zip' => 'array']

// Check if an attribute is in the Model::$jugglable property
if ( $post->isJugglable( 'foo' ) )
{
    // ... foo is not jugglable so this would not get executed
}

// Check if a type is castable
// For this example juggleBar() is not a method.
if ( $post->isJuggleType( 'bar' ) )
{
    // ... this code wouldn't get executed because bar is not a cast type
}

// Throws an exception on invalid cast type
// It's used internally by setJugglable() to enforce valid cast types
$post->checkJuggleType( 'bar' );

// Disable juggling
$post->setJuggling(false); // a value of true would enable it
```

## Soft Deleting Model Trait

This package includes the [`SoftDeletingModelTrait`](https://github.com/esensi/model/blob/master/src/Traits/SoftDeletingModelTrait.php) which implements the [`SoftDeletingModelInterface`](https://github.com/esensi/model/blob/master/src/Contracts/SoftDeletingModelInterface.php) on any `Eloquent` model that uses it. The `SoftDeletingModelTrait` wraps the default `Eloquent` model's `SoftDeletingTrait` for a unified naming convention and stronger interface hinting. The trait also includes the ability to set additional dates in the `$dates` property without having to remember to add `deleted_at`.

Like all the traits, it is self-contained and can be used individually. As a convenience, the [`Esensi\Model\SoftModel`](https://github.com/esensi/model/blob/master/src/Model/SoftModel.php) extends the [`Esensi\Model\Model`](https://github.com/esensi/model/blob/master/src/Model/Model.php) and implements the trait already. The developer can just extend the `SoftModel` and not have to refer to the [Laravel soft deleting documentation](http://laravel.com/docs/eloquent#soft-deleting) again.

> **Pro Tip:** Just because a model uses the `SoftDeletingModelTrait` does not mean that the database has the `deleted_at` column in its table. Be sure to add `$table->softDeletes();` to a [table migration](http://laravel.com/docs/schema#adding-columns).

## Relating Model Trait

This package includes the [`RelatingModelTrait`](https://github.com/esensi/model/blob/master/src/Traits/RelatingModelTrait.php) which implements the [`RelatingModelInterface`](https://github.com/esensi/model/blob/master/src/Contracts/RelatingModelInterface.php) on any `Eloquent` model that uses it. The `RelatingModelTrait` adds methods to `Eloquent` models for automatically resolving related models:

- from simplified configs using the `$relationships` property
- add pivot attributes from simplified configs using the `$relationshipPivots` property
- as magic method calls such as `Post::find($id)->comments()->all()`
- as magic attribute calls such as `Post::find($id)->author`

> **Pro Tip:** As an added bonus, this trait includes a special Eloquent `without()` scope which accepts relationships to remove from the eager loaded list, exactly opposite of the built in Eloquent support for `with()`. This is particularly useful for models that set the `$with` property but occassionally need to remove the eager loading to improve performance on larger queries. This does not impact lazy/manual loading using the dynamic or `load()` methods.

Like all the traits, it is self-contained and can be used individually. Be aware, however, that using this trait does overload the magic `__call()` and `__get()` methods of the model (see [Esensi\Model\Model](https://github.com/esensi/model/blob/master/src/Model.php) source code for how to deal with overloading conflicts). Special credit goes to [Phillip Brown](https://github.com/phillipbrown) and his [Philipbrown/Magniloquent Laravel package](https://github.com/philipbrown/magniloquent) which inspired this trait.

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
     * Using PHP and Laravel's magic, these relationship keys
     * resolve to the actual models automatically.
     *
     * @example relationship bindings:
     *
     *     [ 'hasOne', 'related', 'foreignKey', 'localKey' ]
     *     [ 'hasMany', 'related', 'foreignKey', 'localKey' ]
     *     [ 'hasManyThrough', 'related', 'through', 'firstKey', 'secondKey' ]
     *     [ 'belongsTo', 'related', 'foreignKey', 'otherKey', 'relation' ]
     *     [ 'belongsToMany', 'related', 'table', 'foreignKey', 'otherKey', 'relation' ]
     *     [ 'morphOne', 'related', 'name', 'type', 'id', 'localKey' ]
     *     [ 'morphMany', 'related', 'name', 'type', 'id', 'localKey' ]
     *     [ 'morphTo', 'name', 'type', 'id' ]
     *     [ 'morphToMany', 'related', 'name', 'table', 'foreignKey', 'otherKey', 'inverse' ]
     *     [ 'morphByMany', 'related', 'name', 'table', 'foreignKey', 'otherKey' ]
     *
     * @var array
     */
    protected $relationships = [

        // Bind Comment model as a hasMany relationship.
        // Use $post->comments to query the relationship.
        'comments' => [ 'hasMany', 'Comment' ],

        // Bind User model as a belongsTo relationship.
        // Use $post->author to get the User model.
        'author' => [ 'belongsTo', 'User' ],

        // Bind User model as a belongsTo relationship.
        // Use $post->author to get the User model.
        'tags' => [ 'belongsToMany', 'Tag']
    ];

    /**
     * These are the additional pivot attributes that the model
     * will setup on the relationships that support pivot tables.
     *
     * @var array
     */
    protected $relationshipPivots = [

        // Bind pivot attributes to Tag model when querying the relationship.
        // This is equivalent to $post->tags()->withTimestamps()->withPivot('foo').
        'tags' => [ 'timestamps', 'foo' ]
    ];
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

    // Access the pivot table columns off a
    // many-to-many relationship model.
    $tag = $post->tags()->first();
    $carbon = $tag->pivot->created_at; // Carbon Date
    $bar = $tag->pivot->foo;
});
```



## Unit Testing

The [Esensi](http://github.com/esensi) platform includes other great packages just like this [Esensi/Model](http://github.com/esensi/model) package. This package is currently tagged as `0.5.x` because the other platform packages are not ready for public release. While the others may still be under development, this package already includes features that would be mature enough for a `1.x` release including unit testing and extensive testing in real-world applications.

### Running the Unit Tests

This package uses [PHPUnit](http://phpunit.de) to automate the code testing process. It is included as one of the development dependencies in the `composer.json` file:

```json
{
    "require-dev": {
        "phpunit/phpunit": "4.1.*",
        "mockery/mockery": "0.9.*"
    }
}
```

The test suite can be ran from the command line using the `phpunit` test runner:

```bash
phpunit ./tests
```

> **Important:** There is currently a bug in Laravel (see issue [#1181](https://github.com/laravel/framework/issues/1181)) that prevents model events from firing more than once in a test suite. This means that the first test that uses model tests will pass but any subseqeuent tests will fail. There are a couple of temporary solutions listed in that thread which you can use to make your tests pass in the meantime: namely `Model::flushEventListeners()` and `Model::boot()` after each test runs.

> **Pro Tip:** Please help the open-source community by including good code test coverage with your pull requests. The Esensi development team will review pull requests with unit tests and passing tests as a priority. Significant code changes that do not include unit tests will _not_ be merged.

## Contributing

[Emerson Media](http://www.emersonmedia.com) is proud to work with some of the most talented developers in the PHP community. The developer team welcomes requests, suggestions, issues, and of course pull requests. When submitting issues please be as detailed as possible and provide code examples where possible. When submitting pull requests please follow the same code formatting and style guides that the Esensi code base uses. Please help the open-source community by including good code test coverage with your pull requests. **All pull requests _must_ be submitted to the version branch to which the code changes apply.**

> **Note:** The Esensi team does its best to address all issues on Wednesdays. Pull requests are reviewed in priority followed by urgent bug fixes. Each week the package dependencies are re-evaluated and updates are made for new tag releases.

## Licensing

Copyright (c) 2015 [Emerson Media, LP](http://www.emersonmedia.com)

This package is released under the MIT license. Please see the [LICENSE.txt](https://github.com/esensi/model/blob/master/LICENSE.txt) file distributed with every copy of the code for commercial licensing terms.
