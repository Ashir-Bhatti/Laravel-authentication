<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesPermissionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('roles_permissions', function (Blueprint $table) {
			$table->unsignedBigInteger('role_id')->index()->onDeleteCascade();
			$table->unsignedBigInteger('permission_id')->index()->onDeleteCascade();
			
			$table->primary(['role_id','permission_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('roles_permissions');
	}
}
