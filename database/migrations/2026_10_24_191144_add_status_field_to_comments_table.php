<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            // وضعیت: pending (در انتظار تایید) | approved (تایید شده)
            $table->string('status', 20)->default('pending')->after('body')->index();
            // حذف نرم: کامنت حذف‌شده به «سطل زباله» می‌رود
            $table->softDeletes();
        });

        // کامنت‌های قبلی باید همان‌طور که بودند نمایش داده شوند
        DB::table('comments')->update(['status' => 'approved']);
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropSoftDeletes();
        });
    }
};
