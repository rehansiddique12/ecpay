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
        Schema::create('ipwhitelist', function (Blueprint $table) {
        $table->id();
        $table->string('ip_address')->nullable(false); // Make IP address required
        $table->string('description')->nullable(); // Optional description for the IP
        $table->boolean('is_active')->default(true); // Ability to disable without deleting
        $table->unsignedBigInteger('user_id');
        $table->timestamps();
            $table->foreign('user_id')
                ->references('id')
                ->on('admins')
                ->onDelete('cascade');

            $table->unique(['ip_address', 'user_id']);

        // Index for better performance when querying by user
        $table->index('user_id');
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ipwhitelist');
    }
};
