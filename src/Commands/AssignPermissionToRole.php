<?php

namespace Reflex\Lockdown\Commands;

use Reflex\Lockdown\RoleNotFoundException;
use Reflex\Lockdown\PermissionNotFoundException;
use Reflex\Lockdown\PermissionLevelNotAllowedException;

class AssignPermissionToRole extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name         =   'lockdown:assign-role-perm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description  =   'Assign a permission to a role';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $permissionKey  =   snake_case($this->argument('permission'));
        $roleKey        =   snake_case($this->argument('role'));
        $level          =   $this->argument('level');
        $lockdown       =   $this->lockdown;
        $values         =   [
            'role'  =>  $roleKey,
            'perm'  =>  $permissionKey,
            'level' =>  $level,
        ];
        
        try {
            $role       =   $lockdown->findRoleByKey($roleKey);
            $permission =   $lockdown->findPermissionByKey($permissionKey);
            $result     =   $lockdown->giveRolePermission($role, $permission, $level);
        } catch (RoleNotFoundException $e) {
            $this->error('Role [%(role)s] not found.', $values);
            return;
        } catch (PermissionNotFoundException $e) {
            $this->error('Permission [%(perm)s] not found.', $values);
            return;
        } catch (PermissionLevelNotAllowedException $e) {
            $this->error("Level [%(level)s] given isn't a useable level.", $values);
            return;
        }

        if ($result) {
            $this->info(
                "Permission [%(perm)s] has been assigned to the role.",
                $values
            );
            return;
        }

        $this->error(
            "Permission [%(perm)s] has NOT been assigned to the role due to a system error.",
            $values
        );
    }

    public function getArguments()
    {
        return [
            ['permission', InputArgument::REQUIRED, 'Name of the permission'],
            ['role', InputArgument::REQUIRED, 'Name of the role'],
            ['level', InputArgument::REQUIRED, 'Permission level (Allow, Deny).'],
        ];
    }

}
