<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForeignKeys extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::table('lockdown_permissionables', function($table)
		{
			$table->foreign('permission_id')
				->references('id')
				->on('lockdown_permissions');
		});

		Schema::table('lockdown_user_roles', function($table)
		{
			$table->foreign('role_id')
				->references('id')
				->on('lockdown_roles');

			$table->foreign('user_id')
				->references('id')
				->on('users');
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
		Schema::table('lockdown_permissionables', function($table)
		{
			$table->dropForeign('lockdown_permissionables_permission_id_foreign');
		});

		Schema::table('lockdown_user_roles', function($table)
		{
			$table->dropForeign('lockdown_user_roles_role_id_foreign');
			$table->dropForeign('lockdown_user_roles_user_id_foreign');
		});
	}

}
