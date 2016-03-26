<?php
namespace Reflex\Lockdown\Users\Eloquent;

use Reflex\Lockdown\Roles\RoleInterface;
use Reflex\Lockdown\Users\ProviderInterface;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\UserProvider;

class Provider extends EloquentUserProvider implements UserProvider
{
    /**
     * Find users with permission
     * @param  string|Reflex\Lockdown\Permissions\PermissionInterface $permission
     * @return array
     */
    public function findWithPermission($permission)
    {
        return array_filter(
            $this->findAll(),
            function ($user) use ($permission) {
                return $user->has($permission);
            }
        );
    }

    /**
     * Find users without permission
     * @param  string|Reflex\Lockdown\Permissions\PermissionInterface $permission
     * @return array
     */
    public function findWithoutPermission($permission)
    {
        return array_filter(
            $this->findAll(),
            function ($user) use ($permission) {
                return $user->hasnt($permission);
            }
        );
    }

    /**
     * Find users in role
     * @param  Reflex\Lockdown\Roles\RoleInterface $role
     * @return array
     */
    public function findInRole(RoleInterface $role)
    {
        return $role->users()
            ->get();
    }

    /**
     * Find users not in role
     * @param  Reflex\Lockdown\Roles\RoleInterface $role
     * @return array
     */
    public function findNotInRole(RoleInterface $role)
    {
        return array_filter(
            $this->findAll(),
            function ($user) use ($role) {
                return $user->not($role->key);
            }
        );
    }
}
