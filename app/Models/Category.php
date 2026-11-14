<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\HasSlug;
use App\Casts\JalaliDate;
use App\Traits\HasJalaliDates;


class Category extends Model
{
    use HasSlug;
    use HasJalaliDates;


    protected $fillable = [
        'title',
    ];

    public function posts():hasMany{
        return $this->hasMany(Post::class);
    }
}
