<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\EventGenre;
use App\Enums\EventType;
use App\Enums\EventPrivacy;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->uuid();

            $table->morphs('eventable');
            $table->string('title')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('genre')->default(EventGenre::EVENT->value);
            $table->string('type')->default(EventType::PHYSICAL->value);
            $table->string('address')->nullable();
            $table->timestamp('open_registration')->nullable();
            $table->timestamp('close_registration')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->string('privacy')->default(EventPrivacy::PUBLIC->value);
            $table->mediumText('description')->nullable();

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
        Schema::dropIfExists('events');
    }
};
