<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('lockdown_permissions')) {
			return;
		}

		//
		Schema::create('lockdown_permissions', function($table) {
			$table->increments('id');
			$table->string('name');
			$table->string('key')
				->unique();
			$table->string('description');
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
		Schema::drop('lockdown_permissions');
	}

}
