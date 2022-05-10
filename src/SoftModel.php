<?php

namespace Esensi\Model;

use Esensi\Model\Contracts\SoftDeletingModelInterface;
use Esensi\Model\Traits\SoftDeletingModelTrait;

/**
 * Soft Deleting Model.
 *
 */
abstract class SoftModel extends Model implements SoftDeletingModelInterface
{
    /*
     * Make model use soft deletes.
     *
     * @see Esensi\Model\Traits\SoftDeletingModelTrait
     */
    use SoftDeletingModelTrait;
}
