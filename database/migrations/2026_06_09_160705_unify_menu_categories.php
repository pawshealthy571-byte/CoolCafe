<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::table('menus')->where('category', 'Beverages')->update(['category' => 'Snack & Minuman']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse as the category change is desirable.
    }
};
