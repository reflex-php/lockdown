<?php

namespace Reflex\Lockdown\Commands;

use Reflex\Lockdown\PermissionNotFoundException;
use Reflex\Lockdown\UserNotFoundException;

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
        } catch (UserNotFoundException $e) {
            $this->error('User [%(id)d] not found.', $values);
            return;
        } catch (PermissionNotFoundException $e) {
            $this->error('Permission [%(perm)s] not found', $values);
            return;
        }

        $result =   $lockdown->removeUserPermission($user, $permission);

        if ($result) {
            $this->info("Permission [%(perm)s] has been removed from the user.", $values);
            return;
        }

        $this->error("Permission [%(perm)s] has NOT been removed from the user due to a system error.", $values);
    }

    public function getArguments()
    {
        return [
            ['permission', InputArgument::REQUIRED, 'Name of the permission'],
            ['id', InputArgument::REQUIRED, 'ID used to look up user.'],
        ];
    }

}
