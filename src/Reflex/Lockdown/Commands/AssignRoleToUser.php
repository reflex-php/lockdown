<?php
/**
 * Lockdown ACL
 *
 * @category Package
 * @package  Reflex
 * @author   Mike Shellard <contact@mikeshellard.me>
 * @license  http://mikeshellard.me/reflex/license MIT
 * @link     http://mikeshellard.me/reflex/lockdown
 */

namespace Reflex\Lockdown\Commands;

use Reflex\Lockdown\Exceptions\UserNotFound;
use Reflex\Lockdown\Exceptions\RoleNotFound;

/**
 * Assign a role to a user
 * @category Package
 * @package  Reflex
 * @author   Mike Shellard <contact@mikeshellard.me>
 * @license  http://mikeshellard.me/reflex/license MIT
 * @link     http://mikeshellard.me/reflex/lockdown
 */
class AssignRoleToUser extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name         =   'lockdown:assign-role-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description  =   'Assign a role to a user';

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
            $user   =   $lockdown->findUserById($id);
            $role   =   $lockdown->findRoleByKey($roleKey);
            $result =   $lockdown->giveUserRole($user, $role);
        } catch (UserNotFound $e) {
            $this->error('User [%(id)d] not found.', $values);
            return;
        } catch (RoleNotFound $e) {
            $this->error('Role [%(role)s] not found', $values);
            return;
        }

        if ($result) {
            $this->info(
                "Role [%(role)s] has been assigned to the user.",
                $values
            );
            return;
        }

        $this->error(
            "Role [%(role)s] has NOT been assigned to the user due to a " .
            "system error.",
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
            ['id', InputArgument::REQUIRED, 'ID used to lookup user'],
        ];
    }
}
