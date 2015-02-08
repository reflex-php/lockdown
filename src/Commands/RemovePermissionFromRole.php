<?php

namespace Reflex\Lockdown\Commands;

use Reflex\Lockdown\PermissionNotFoundException;
use Reflex\Lockdown\RoleNotFoundException;

class RemovePermissionFromRole extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name         =   'lockdown:remove-role-perm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description  =   'Remove a permission from a role';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $permissionKey  =   snake_case($this->argument('permission'));
        $roleKey        =   snake_case($this->argument('role'));
        $lockdown       =   $this->lockdown;
        $values         =   [
            'perm'  =>  $permissionKey,
            'role'  =>  $roleKey,
        ];

        try {
            $role       =   $lockdown->findRoleByKey($roleKey);
            $permission =   $lockdown->findPermissionByKey($permissionKey);
            $result     =   $lockdown->removeRolePermission($role, $permission);
        } catch (RoleNotFoundException $e) {
            $this->error('Role [%(role)s] not found', $values);
            return;
        } catch (PermissionNotFoundException $e) {
            $this->error('Permission [%(perm)s] not found', $values);
            return;
        }

        if ($result) {
            $this->info("Permission [%(perm)s] has been removed from the role.", $values);
            return;
        }
        
        $this->error("Permission [%(perm)s] has NOT been removed from the role due to a system error.", $values);
    }

    public function getArguments()
    {
        return [
            ['permission', InputArgument::REQUIRED, 'Name of the permission'],
            ['role', InputArgument::REQUIRED, 'Name of the role.'],
        ];
    }

}
