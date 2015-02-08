<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('lockdown_user_roles')) {
			return;
		}

		Schema::create('lockdown_user_roles', function($table) {
			$table->unsignedInteger('role_id');

			$table->unsignedInteger('user_id');

			$table->foreign('role_id')
				->references('id')
				->on('lockdown_roles');

			$table->foreign('user_id')
				->references('id')
				->on('users');

			$table->timestamps();
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
		Schema::drop('lockdown_user_roles');
	}

}
