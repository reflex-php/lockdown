<?php

namespace Reflex\Lockdown\Commands;

use Reflex\Lockdown\RoleNotFoundException;
use Reflex\Lockdown\UserNotFoundException;

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
        } catch (UserNotFoundException $e) {
            $this->error('User [%(id)d] not found.', $values);
            return;
        } catch (RoleNotFoundException $e) {
            $this->error('Role [%(role)s] not found', $values);
            return;
        }

        $result =   $lockdown->removeUserRole($user, $role);

        if ($result) {
            $this->info("Role [%(role)s] has been removed from the user.", $values);
            return;
        }
        
        $this->error("Permission [%(role)s] has NOT been removed from the user due to a system error.", $values);
    }

    public function getArguments()
    {
        return [
            ['role', InputArgument::REQUIRED, 'Name of the role'],
            ['criteria', InputArgument::REQUIRED, 'ID used to lookup user'],
        ];
    }

}
