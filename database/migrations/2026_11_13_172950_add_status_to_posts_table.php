<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('posts', 'status')) {
            Schema::table('posts', function (Blueprint $table) {
                // draft | published | scheduled | archived  (مقدارها در App\Enums\PostStatus)
                $table->string('status', 20)->default('published')->after('image');
                // زمان انتشار: برای پست‌های منتشرشده = زمان انتشار؛ برای زمان‌بندی‌شده = زمان آینده
                $table->timestamp('published_at')->nullable()->after('status');

                $table->index(['status', 'published_at']);
            });
        }

        // همه‌ی پست‌های موجود «منتشرشده»‌اند و تاریخ انتشارشان همان تاریخ ساختشان است
        DB::table('posts')->whereNull('published_at')->update(['published_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['status', 'published_at']);
            $table->dropColumn(['status', 'published_at']);
        });
    }
};
