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
use Reflex\Lockdown\Exceptions\UserNotFound;

/**
 * Remove permission from user
 * @category Package
 * @package  Reflex
 * @author   Mike Shellard <contact@mikeshellard.me>
 * @license  http://mikeshellard.me/reflex/license MIT
 * @link     http://mikeshellard.me/reflex/lockdown
 */
class RemovePermissionFromUser extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name         =   'lockdown:remove-perm-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description  =   'Remove a permission from a user';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $permissionKey  =   snake_case($this->argument('permission'));
        $id             =   $this->argument('id');
        $lockdown       =   $this->lockdown;
        $values         =   [
            'perm'  =>  $permissionKey,
            'id'    =>  $id,
        ];

        try {
            $user       =   $lockdown->findUserById($id);
            $permission =   $lockdown->findPermissionByKey($permissionKey);
        } catch (UserNotFound $e) {
            $this->error('User [%(id)d] not found.', $values);
            return;
        } catch (PermissionNotFound $e) {
            $this->error('Permission [%(perm)s] not found', $values);
            return;
        }

        $result =   $lockdown->removeUserPermission($user, $permission);

        if ($result) {
            $this->info(
                "Permission [%(perm)s] has been removed from the user.",
                $values
            );
            return;
        }

        $this->error(
            "Permission [%(perm)s] has NOT been removed from the user due to" .
            " a system error.",
            $values
        );
    }

    /**
     * Get command line arguments
     * @return array
     */
    public function getArguments()
    {
        return [
            ['permission', InputArgument::REQUIRED, 'Name of the permission'],
            ['id', InputArgument::REQUIRED, 'ID used to look up user.'],
        ];
    }
}
