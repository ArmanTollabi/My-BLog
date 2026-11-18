<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where(fn ($q) => $q->whereNull('role')->orWhere('role', ''))
            ->update(['role' => 'subscriber']);
    }

    public function down(): void
    {
        // برگشت‌ناپذیر عمداً: نمی‌دانیم کدام کاربران قبلاً خالی بودند.
    }
};
