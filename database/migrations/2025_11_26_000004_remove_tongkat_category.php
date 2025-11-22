<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('categories')) {
            return;
        }

        DB::table('categories')
            ->whereRaw('LOWER(name) = ?', ['tongkat'])
            ->delete();
    }

    public function down(): void
    {
        if (!Schema::hasTable('categories')) {
            return;
        }

        $exists = DB::table('categories')
            ->whereRaw('LOWER(name) = ?', ['tongkat'])
            ->exists();

        if ($exists) {
            return;
        }

        DB::table('categories')->insert([
            'name' => 'Tongkat',
            'description' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
