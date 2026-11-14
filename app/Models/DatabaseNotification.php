<?php

namespace App\Models;

use App\Traits\HasJalaliDates;
use Illuminate\Notifications\DatabaseNotification as BaseNotification;

class DatabaseNotification extends BaseNotification
{
    use HasJalaliDates;
}
