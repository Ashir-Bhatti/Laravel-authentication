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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->uuid();

            $table->string('title')->index()->nullable();
            $table->mediumText('description')->nullable();
            $table->string('tenant_id')->index();
            $table->string('address')->nullable();
            $table->string('registration_number')->index()->nullable();
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
			$table->tinyInteger('status')->default(1);

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
        Schema::dropIfExists('organizations');
    }
};
