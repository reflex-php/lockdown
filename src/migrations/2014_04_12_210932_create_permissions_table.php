<?php
/**
 * Lockdown ACL
 *
 * PHP version 5.4
 *
 * @category Package
 * @package  Reflex
 * @author   Mike Shellard <contact@mikeshellard.me>
 * @license  http://mikeshellard.me/reflex/license MIT
 * @link     http://mikeshellard.me/reflex/lockdown
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Create the permissions table
 * @category Package
 * @package  Reflex
 * @author   Mike Shellard <contact@mikeshellard.me>
 * @license  http://mikeshellard.me/reflex/license MIT
 * @link     http://mikeshellard.me/reflex/lockdown
 */
class CreatePermissionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'lockdown_permissions',
            function ($table) {
                $table->increments('id');
                $table->string('name');
                $table->string('key')
                    ->unique();
                $table->string('description');
                $table->timestamps();
            }
        );
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
