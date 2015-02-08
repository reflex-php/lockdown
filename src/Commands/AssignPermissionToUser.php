<?php
namespace Reflex\Lockdown\Commands;

use Reflex\Lockdown\UserNotFoundException;
use Reflex\Lockdown\PermissionNotFoundException;
use Reflex\Lockdown\PermissionLevelNotAllowedException;

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
            $result     =   $lockdown->giveUserPermission($user, $permission, $level);
        } catch (PermissionNotFoundException $e) {
            $this->error('Permission [%(perm)s] not found', $values);
            return;
        } catch (UserNotFoundException $e) {
            $this->error('User [%(id)d] not found.', $values);
            return;
        } catch (PermissionLevelNotAllowedException $e) {
            $this->error("Level [%(level)s] given isn't a useable level.", $values);
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
            "Permission [%(perm)s] has NOT been assigned to the user due to a system error.",
            $values
        );
    }

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
