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
        Schema::create('subscription_details', function (Blueprint $table) {
            $table->id();
            $table->uuid();

            $table->foreignId('organization_id')->constrained()->index();
            $table->foreignId('subscription_id')->cascadeOnDelete()->index();
            $table->date('billing_start_date')->nullable();
            $table->integer('setup_fee')->nullable();
            $table->date('sub_expire_date')->nullable();
            $table->integer('total')->nullable();
            $table->enum('status', ['active', 'in-processing', 'cancel', 'pause'])->default('active');
            $table->date('pause_start_date')->nullable();
            $table->integer('pause_subscription_months')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_details');
    }
};
