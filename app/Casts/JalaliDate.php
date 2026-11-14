<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class JalaliDate implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Jalalian
    {
        return $value ? Jalalian::fromDateTime($value) : null;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value instanceof Jalalian) {
            return $value->toCarbon()->toDateTimeString();
        }

        return $value;
    }
}
