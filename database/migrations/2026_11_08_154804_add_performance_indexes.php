<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ایندکس ستون‌های پرکاربرد (فیلتر، مرتب‌سازی، join). امن و تکرارپذیر است:
 * جدول/ستون ناموجود را رد می‌کند و اگر همان ایندکس از قبل (مثلاً از foreign key) باشد، دوباره نمی‌سازد.
 * ایندکس فقط سرعت می‌دهد؛ داده‌ها را عوض نمی‌کند.
 */
return new class extends Migration
{
    private function map(): array
    {
        return [
            'posts'      => [['category_id'], ['user_id'], ['created_at']],
            'comments'   => [['post_id', 'status'], ['user_id'], ['parent_id'], ['status', 'created_at'], ['deleted_at']],
            'post_likes' => [['post_id', 'user_id'], ['user_id']],
            'post_saves' => [['post_id', 'user_id'], ['user_id']],
            'post_tag'   => [['post_id', 'tag_id'], ['tag_id']],
            'users'      => [['role']],
        ];
    }

    private function name(string $table, array $cols): string
    {
        return $table . '_' . implode('_', $cols) . '_idx';
    }

    public function up(): void
    {
        foreach ($this->map() as $table => $indexes) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($indexes as $cols) {
                if (! Schema::hasColumns($table, $cols) || Schema::hasIndex($table, $cols)) {
                    continue;
                }

                Schema::table($table, fn (Blueprint $t) => $t->index($cols, $this->name($table, $cols)));
            }
        }
    }

    public function down(): void
    {
        foreach ($this->map() as $table => $indexes) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($indexes as $cols) {
                $name = $this->name($table, $cols);

                if (Schema::hasIndex($table, $name)) {
                    Schema::table($table, fn (Blueprint $t) => $t->dropIndex($name));
                }
            }
        }
    }
};
