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
        Schema::create('event_reminders', function (Blueprint $table) {
            $table->id();
            $table->uuid();

            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->timestamp('remind_at')->nullable();
            $table->boolean('is_sent')->default(false);

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
        Schema::dropIfExists('event_reminders');
    }
};
