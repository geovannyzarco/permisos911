<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DateTimeOffsetCast implements CastsAttributes
{
    /**
     * Cast the given value from database.
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value)) {
            return null;
        }

        // Carbon parses the offset from the database (e.g. +00:00 or -06:00).
        // We convert it to the application's timezone so local times are kept correct.
        return Carbon::parse($value)->setTimezone(config('app.timezone'));
    }

    /**
     * Prepare the given value for storage.
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value)) {
            return null;
        }

        $date = $value instanceof Carbon ? $value : Carbon::parse($value);

        // Format to string with the offset (e.g. '2026-05-29 08:30:00.000 -06:00')
        return $date->format('Y-m-d H:i:s.v P');
    }
}
