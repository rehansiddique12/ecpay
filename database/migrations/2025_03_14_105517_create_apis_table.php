<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apis', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('username', 100);
            $table->string('email', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('password');
            $table->tinyInteger('status')->default(1);
            $table->string('website', 255)->nullable();
            $table->string('api_endpoint_deposit', 200)->nullable();
            $table->string('api_endpoint_withdrawal', 200)->nullable();
            $table->longText('admin_access');
            $table->string('type', 50);
            $table->string('api_key');
            $table->string('last_login', 50);
            $table->string('remember_token', 100);
            $table->string('image', 191);
            $table->decimal('balance', 12, 2);
            $table->decimal('min_deposit', 8, 2)->default(0.00);
            $table->decimal('min_withdrawal', 8, 2)->default(0.00);
            $table->string('acc_type', 20)->default('Partner');
            $table->integer('parent_id');
            $table->tinyInteger('sign')->default(0);
            $table->string('secret_key', 255)->nullable();
            $table->tinyInteger('txn_verification')->default(0);
            $table->string('redirect_url', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apis');
    }
};
