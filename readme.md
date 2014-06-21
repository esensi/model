## Esensi Model Traits Package

An [Esensi](https://github.com/esensi) package, coded by [Emerson Media](http://www.emersonmedia.com).

> **Want to work with us on great Laravel applications?**
Email us at [careers@emersonmedia.com](careers@emersonmedia.com)

The `Esensi/Model` package is just one package that makes up [Esensi](https://github.com/esensi), a platform built on [Laravel](http://laravel.com). This package bundles [PHP traits](http://php.net/traits) that extend Laravel's default Eloquent models and traits. Using traits allows for a high-degree of code reusability and extensibility. While this package provides some reasonable base models, as a developer you're free to mix and match traits in any combination you need, being confident that the code complies to a reliable interface and is properly unit tested. For more details on the inner workings of the traits please consult the generously documented source code.

> **Have a project in mind?** _Email us at [sales@emersonmedia.com](sales@emersonmedia.com), or call 1.877.439.6665._

### Quick Start

> **Notice:** _This code is specifically designed to be compatible with the [Laravel Framework](http://laravel.com) and may not be compatible as a stand-alone dependency or as part of another framework._

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

Maybe you would like your blog to use Laravel's soft deletes for your `Post` model allowing you to trash and restore your articles. Simply swap out the `Esensi\Model\Model` with the soft deleting version `Esensi\Model\SoftModel` like so:

```php
<?php

use \Esensi\Model\SoftModel;

class Post extends SoftModel {

}
```

> **Tip:** While Laravel includes `SoftDeletingTrait`, Esensi expands upon this by also forcing the trait to comply with a [`SoftDeletingModelInterface` contract](https://github.com/esensi/model/blob/0.3/src/Contracts/SoftDeletingModelInterface.php). This ensures a higher level of compatibility and code integrity.
