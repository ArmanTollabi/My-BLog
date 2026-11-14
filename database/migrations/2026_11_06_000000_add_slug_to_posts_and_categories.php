<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ۱) ستون را فقط اگر وجود ندارد اضافه کن (اگر از قبل هست، دست نمی‌زنیم)
        foreach (['posts', 'categories','tags'] as $table) {
            if (! Schema::hasColumn($table, 'slug')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->string('slug')->nullable()->after('title');
                });
            }
        }

        // ۲) فقط برای ردیف‌هایی که slug ندارند (null یا خالی) slug بساز
        foreach ([Post::class, Category::class, Tag::class] as $model) {
            $model::query()
                ->where(fn ($q) => $q->whereNull('slug')->orWhere('slug', ''))
                ->orderBy('id')
                ->each(function ($row) {
                    $row->slug = $row->makeUniqueSlug($row->title);
                    $row->saveQuietly();
                });
        }

        // ۳) ایندکس یکتا را فقط اگر وجود ندارد بساز
        foreach (['posts', 'categories','tags'] as $table) {
            if (! Schema::hasIndex($table, 'slug', 'unique')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->unique('slug');
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['posts', 'categories','tags'] as $table) {
            if (Schema::hasIndex($table, 'slug', 'unique')) {
                Schema::table($table, fn (Blueprint $t) => $t->dropUnique(['slug']));
            }
            if (Schema::hasColumn($table, 'slug')) {
                Schema::table($table, fn (Blueprint $t) => $t->dropColumn('slug'));
            }
        }
    }
};
