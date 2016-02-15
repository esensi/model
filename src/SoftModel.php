<?php

namespace Esensi\Model;

use Esensi\Model\Contracts\SoftDeletingModelInterface;
use Esensi\Model\Traits\SoftDeletingModelTrait;

/**
 * Soft Deleting Model.
 *
 * @author Daniel LaBarge <daniel@emersonmedia.com>
 * @copyright 2015-2016 Emerson Media LP
 * @license https://github.com/esensi/model/blob/master/license.md MIT License
 *
 * @link http://www.emersonmedia.com
 * @see Esensi\Model\Model
 * @see Esensi\Model\Contracts\SoftDeletingModelInterface
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
