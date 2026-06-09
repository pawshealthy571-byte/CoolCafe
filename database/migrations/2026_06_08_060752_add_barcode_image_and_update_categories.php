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
        Schema::table('menus', function (Blueprint $table) {
            $table->string('barcode_image')->nullable()->after('barcode');
        });

        \App\Models\Menu::whereIn('category', ['Snack', 'Minuman'])->update(['category' => 'Snack & Minuman']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('barcode_image');
        });
    }
};
