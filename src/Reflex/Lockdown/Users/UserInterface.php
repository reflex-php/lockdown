<?php
namespace Reflex\Lockdown\Users;

use Reflex\Lockdown\Permissions\PermissionInterface;
use Reflex\Lockdown\Roles\RoleInterface;

interface UserInterface
{
    /**
     * Retrieve all users roles
     * @return mixed
     */
    public function roles();

    /**
     * Retrieve all users permissions
     * @return mixed
     */
    public function permissions();

    /**
     * Is the user a part of the role?
     * @param  string|array $roles
     * @param  boolean      $all
     * @return boolean
     */
    public function is($roles, $all = true);

    /**
     * Is the user not a part of the role?
     * @param  string|array $roles Role name to lookup
     * @return boolean
     */
    public function not($roles, $all = true);

    /**
     * Does the user have the permission?
     * @param  string|array $permissions Permission to lookup
     * @return boolean
     */
    public function has($permissions, $all = true);

    /**
     * Does the user not have the permission?
     * @param  string|array $permissions Permissions to lookup
     * @return boolean
     */
    public function hasnt($permissions, $all = true);

    /**
     * Get the actual permission level and inheritance type
     * @param  string $permission Permission name/key
     * @return mixed
     */
    public function getPermission($permission);

    /**
     * Give a permission to the user
     * @param  PermissionInterface $permission
     * @param  string              $level
     * @return boolean
     */
    public function give(PermissionInterface $permission, $level = 'allow');

    /**
     * Remove a permission from the user
     * @param  PermissionInterface $permission
     * @return boolean
     */
    public function remove(PermissionInterface $permission);

    /**
     * Join a role
     * @param  RoleInterface $role
     * @return boolean
     */
    public function join(RoleInterface $role);

    /**
     * Leave a role
     * @param  RoleInterface $role
     * @return boolean
     */
    public function leave(RoleInterface $role);

    /**
     * Delete user and associated information
     * @return boolean
     */
    public function delete();
}
