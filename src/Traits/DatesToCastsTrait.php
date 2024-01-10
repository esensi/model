<?php

namespace Esensi\Model\Traits;

trait DatesToCastsTrait
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    protected static function bootDatesToCastsTrait()
    {
        $events = ['saving', 'retrieved', 'deleting'];

        foreach ($events as $event) {
            static::$event(function ($model) {
                $model->convertDatesToCasts();
            });
        }
    }

    /**
     * Convert $dates to $casts automatically.
     *
     * @return void
     */
    protected function convertDatesToCasts()
    {
        if (property_exists($this, 'dates') && property_exists($this, 'casts')) {
            foreach ($this->dates as $dateColumn) {
                if (!array_key_exists($dateColumn, $this->casts)) {
                    $this->casts[$dateColumn] = 'datetime';
                }
            }
        }
    }
}
