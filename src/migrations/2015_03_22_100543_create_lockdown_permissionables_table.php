<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLockdownPermissionablesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('lockdown_permissionables', function($table)
		{
			$table->unsignedInteger('permission_id');
			$table->unsignedInteger('permissionable_id');
			$table->string('permissionable_type');
			$table->enum('level', ['deny', 'allow'])->default('allow');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
		Schema::drop('lockdown_permissionables');
	}

}
