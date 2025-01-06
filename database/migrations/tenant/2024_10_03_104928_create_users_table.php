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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid();

			$table->string('fname')->index()->nullable();
			$table->string('lname')->index()->nullable();
			$table->string('username')->index()->unique();
			$table->string('phone');
			$table->rememberToken();
			$table->string('email')->index()->unique();
			$table->string('address')->nullable();
			$table->string('city')->nullable();
			$table->string('state')->nullable();
			$table->string('zip_code')->nullable();
            $table->foreignId('position_board_id')->nullable()->constrained();
            // $table->foreignId('constituent_id')->nullable()->constrained();
            $table->timestamp('term_start_date')->nullable();
            $table->timestamp('term_end_date')->nullable();
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
        Schema::dropIfExists('users');
    }
};
