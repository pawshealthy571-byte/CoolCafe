<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('menus')->where('category', 'Snack & Minuman')->update(['category' => 'Snack']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('menus')->whereIn('category', ['Snack', 'Minuman'])->update(['category' => 'Snack & Minuman']);
    }
};
