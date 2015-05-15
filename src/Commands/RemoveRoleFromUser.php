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

use Reflex\Lockdown\Exceptions\RoleNotFound;
use Reflex\Lockdown\Exceptions\UserNotFound;

/**
 * Remove a role from user
 * @category Package
 * @package  Reflex
 * @author   Mike Shellard <contact@mikeshellard.me>
 * @license  http://mikeshellard.me/reflex/license MIT
 * @link     http://mikeshellard.me/reflex/lockdown
 */
class RemoveRoleFromUser extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name         =   'lockdown:remove-role-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description  =   'Remove a role from a user';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $roleKey    =   snake_case($this->argument('role'));
        $id         =   $this->argument('id');
        $lockdown   =   $this->lockdown;
        $values     =   [
            'role'  =>  $roleKey,
            'id'    =>  $id,
        ];

        try {
            $user       =   $lockdown->findUserById($id);
            $permission =   $lockdown->findRoleByKey($permissionKey);
        } catch (UserNotFound $e) {
            $this->error('User [%(id)d] not found.', $values);
            return;
        } catch (RoleNotFound $e) {
            $this->error('Role [%(role)s] not found', $values);
            return;
        }

        $result =   $lockdown->removeUserRole($user, $role);

        if ($result) {
            $this->info(
                "Role [%(role)s] has been removed from the user.",
                $values
            );
            return;
        }
        
        $this->error(
            "Permission [%(role)s] has NOT been removed from the user due" .
            " to a system error.",
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
            ['role', InputArgument::REQUIRED, 'Name of the role'],
            ['criteria', InputArgument::REQUIRED, 'ID used to lookup user'],
        ];
    }
}
