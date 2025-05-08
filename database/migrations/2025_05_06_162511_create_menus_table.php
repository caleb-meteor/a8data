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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('');
            $table->string('type')->default('');
            $table->string('meta')->nullable();
            $table->string('permission')->default('')->index();
            $table->unsignedBigInteger('sort')->default(0);
            $table->unsignedBigInteger('pid')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('user_menus', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->default(0)->index();
            $table->unsignedBigInteger('menu_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
        Schema::dropIfExists('user_menus');
    }
};
