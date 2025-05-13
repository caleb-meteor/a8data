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
        Schema::create('finances', function (Blueprint $table) {
            $table->id();
            $table->integer('month')->nullable();
            $table->date('date')->nullable()->index();
            $table->integer('department_id')->default(0);
            $table->decimal('counterparty_fee', 18, 6)->default(0)->comment('第三方费用');
            $table->decimal('media_fee', 18, 6)->default(0)->comment('媒体费');
            $table->unsignedBigInteger('team_id')->default(0)->index();
            $table->unsignedBigInteger('product_id')->default(0)->index();
            $table->unsignedBigInteger('agent_id')->default(0)->index();
            $table->decimal('transaction_fee', 18, 6)->default(0)->comment('手续费');
            $table->decimal('service_fee', 18, 6)->default(0)->comment('服务费');
            $table->decimal('usd_loss_percent', 12, 6)->default(0)->comment('U损');
            $table->decimal('usd', 18, 6)->default(0);
            $table->decimal('ustd', 18, 6)->default(0);
            $table->decimal('commission', 18, 6)->default(0)->comment('返点');
            $table->string('purpose')->default('')->comment('用途');
            $table->string('description')->default('')->comment('费用明细');
            $table->string('account')->default('')->comment('账户信息');
            $table->string('handler')->default('')->comment('经手人');
            $table->string('remark')->default('')->comment('备注');
            $table->decimal('balance', 18, 6)->default(0)->comment('余额');
            $table->unsignedBigInteger('creator_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finances');
    }
};
