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
        Schema::table('products', function (Blueprint $table) {
            $table->string('discount_type')->nullable()->after('price_cents');
            $table->decimal('discount_percent', 5, 2)->nullable()->after('discount_type');
            $table->unsignedInteger('discount_amount_cents')->nullable()->after('discount_percent');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_percent', 'discount_amount_cents']);
        });
    }
};
