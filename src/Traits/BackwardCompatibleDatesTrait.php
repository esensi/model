<?php

namespace Esensi\Model\Traits;

trait BackwardCompatibleDatesTrait
{

    /**
     * Get the attributes that should be converted to dates.
     *
     * @return array
     */
    public function getDates()
    {
        $defaults = parent::getDates();
        return array_unique(array_merge($this->dates, $defaults));
    }
}
