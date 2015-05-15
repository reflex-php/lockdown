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

namespace Reflex\Lockdown\Commands;

use Reflex\Lockdown\Exceptions\PermissionNotFound;

/**
 * Delete a permission
 * @category Package
 * @package  Reflex
 * @author   Mike Shellard <contact@mikeshellard.me>
 * @license  http://mikeshellard.me/reflex/license MIT
 * @link     http://mikeshellard.me/reflex/lockdown
 */
class DeletePermission extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name         =   'lockdown:delete-permission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description  =   'Delete a permission';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $permissionKey  =   snake_case($this->argument('permission'));
        $lockdown       =   $this->lockdown;
        $values         =   [
            'permission'    =>  $permissionKey
        ];

        try {
            $permission =   $lockdown->findPermissionByKey($permissionKey);
        } catch (PermissionNotFound $e) {
            $this->error(
                "Permission [%(permission)s] doesn't exist",
                $values
            );
            return;
        }

        if ($lockdown->deletePermission($permissionKey)) {
            $this->info(
                "The permission [%(permission)s] has been deleted!",
                $values
            );
            return;
        }

        $this->error(
            "The permission [%(permission)s] couldn't be deleted",
            $values
        );
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['permission', InputArgument::REQUIRED, 'Name of the permission'],
        ];
    }
}
