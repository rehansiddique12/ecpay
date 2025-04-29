<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('account_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('image')->nullable();
            $table->boolean('status')->default(1);
            $table->json('currencies')->nullable();
            $table->string('currency', 100)->nullable();
            $table->string('symbol', 50)->nullable();
            $table->decimal('min_amount', 18, 8)->nullable();
            $table->decimal('max_amount', 18, 8)->nullable();
            $table->decimal('percentage_charge', 18, 8)->nullable();
            $table->decimal('fixed_charge', 18, 8)->nullable();
            $table->decimal('convention_rate', 18, 8)->nullable();
            $table->decimal('minimum_withdrawal_amount', 18, 8)->nullable();
            $table->decimal('maximum_withdrawal_amount', 18, 8)->nullable();
            $table->json('parameters')->nullable();
            $table->json('extra_parameters')->nullable();
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_gateways');
    }
}
