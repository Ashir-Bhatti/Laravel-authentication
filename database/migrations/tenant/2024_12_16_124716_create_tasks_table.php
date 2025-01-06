<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\TaskStatus;
use App\Enums\TaskPriority;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('title')->index();
            $table->text('description')->nullable();
            $table->string('status')->default(TaskStatus::TO_DO->value);
            $table->string('priority')->default(TaskPriority::LOW->value);
            $table->foreignId('assignee_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('due_date')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('important_at')->nullable();
            $table->timestamp('favorite_at')->nullable();
            $table->timestamp('completed_at')->nullable();
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
        Schema::dropIfExists('tasks');
    }
};
