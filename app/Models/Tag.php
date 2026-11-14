<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Casts\JalaliDate;
use App\Traits\HasJalaliDates;
use App\Models\Concerns\HasSlug;

class Tag extends Model
{
    use HasJalaliDates;
    use HasSlug;


    protected $fillable = [
        'title',
    ];

    public function posts(): BelongsToMany{
        return $this->belongsToMany(Post::class);
    }
}
