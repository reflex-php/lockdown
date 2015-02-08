<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyToPolymorphic extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// DB::table('lockdown_permissionables')->all()->delete();

		Schema::create('lockdown_permissionables', function(Blueprint $table) {
			$table->unsignedInteger('permission_id');
			$table->foreign('permission_id')
				->references('id')
				->on('lockdown_permissions');
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
		DB::table('lockdown_permissionables')->truncate();

		Schema::drop('lockdown_permissionables');
	}

}
