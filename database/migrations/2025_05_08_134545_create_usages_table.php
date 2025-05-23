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
        Schema::create('usages', function (Blueprint $table) {
            $table->id();
            $table->integer('month')->nullable();
            $table->date('date')->nullable()->index();
            $table->integer('department_id')->default(0);
            $table->unsignedBigInteger('team_id')->default(0)->index();
            $table->unsignedBigInteger('product_id')->default(0)->index();
            $table->string('exclusive_agent')->default('')->comment('总代理');
            $table->string('channel')->default('');
            $table->integer('media')->default(0);
            $table->unsignedBigInteger('agent_id')->default(0)->index();
            $table->string('placement_method')->default('')->comment('投放方式');
            $table->integer('actual_usage')->default(0)->comment('实际消耗');
            $table->integer('view')->default(0)->comment('展示');
            $table->integer('click')->default(0)->comment('点击');
            $table->integer('install')->default(0)->comment('安装');
            $table->integer('send_num')->default(0)->comment('发送条数');
            $table->decimal('price', 14, 6)->default(0)->comment('单价');
            $table->string('unique_id')->default('');
            $table->unsignedBigInteger('creator_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usages');
    }
};
