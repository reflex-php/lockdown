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

use Reflex\Lockdown\Exceptions\UserNotFound;
use Reflex\Lockdown\Exceptions\PermissionNotFound;
use Reflex\Lockdown\Exceptions\PermissionLevelNotAllowed;

/**
 * AssignPermissionToUser
 * @category Package
 * @package  Reflex
 * @author   Mike Shellard <contact@mikeshellard.me>
 * @license  http://mikeshellard.me/reflex/license MIT
 * @link     http://mikeshellard.me/reflex/lockdown
 */
class AssignPermissionToUser extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name         =   'lockdown:assign-perm-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description  =   'Assign a permission to a user';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $permissionKey  =   snake_case($this->argument('permission'));
        $level          =   $this->argument('level');
        $id             =   $this->argument('id');
        $lockdown       =   $this->lockdown;
        $values         =   [
            'perm'  =>  $permissionKey,
            'level' =>  $level,
            'id'    =>  $id,
        ];

        try {
            $user       =   $lockdown->findUserById($id);
            $permission =   $lockdown->findPermissionByKey($permissionKey);
            $result     =   $lockdown->giveUserPermission(
                $user,
                $permission,
                $level
            );
        } catch (PermissionNotFound $e) {
            $this->error('Permission [%(perm)s] not found', $values);
            return;
        } catch (UserNotFound $e) {
            $this->error('User [%(id)d] not found.', $values);
            return;
        } catch (PermissionLevelNotAllowed $e) {
            $this->error(
                "Level [%(level)s] given isn't a usable level.",
                $values
            );
            return;
        }
        
        if ($result) {
            $this->info(
                "Permission [%(perm)s] has been assigned to the user.",
                $values
            );
            return;
        }

        $this->error(
            "Permission [%(perm)s] has NOT been assigned to the user due to " .
            "a system error.",
            $values
        );
    }

    /**
     * Get arguments for command
     * @return array
     */
    public function getArguments()
    {
        return [
            ['permission', InputArgument::REQUIRED, 'Name of the permission'],
            [
                'id',
                InputArgument::REQUIRED,
                'ID to lookup user.',
            ],
            [
                'level',
                InputArgument::REQUIRED,
                'Permission level (Allow, Deny, Inherit).',
            ],
        ];
    }
}
