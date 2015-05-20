<?php
/**
 * Lockdown Package
 *
 * @package   Lockdown
 * @version   1.0.0
 * @author    Reflex
 * @copyright Reflex 2014
 * @link      http://aziri.us/reflex/
 */

namespace Reflex\Lockdown;

use Reflex\Lockdown\Users\ProviderInterface as UserProviderInterface;
use Reflex\Lockdown\Users\UserInterface;
use Reflex\Lockdown\Roles\ProviderInterface as RoleProviderInterface;
use Reflex\Lockdown\Roles\RoleInterface;
use Reflex\Lockdown\Permissions\ProviderInterface as PermissionProviderInterface;
use Reflex\Lockdown\Permissions\PermissionInterface;
use Reflex\Lockdown\Exceptions\PermissionNotFound;
use Reflex\Lockdown\Exceptions\PermissionLevelNotAllowed;
use Reflex\Lockdown\Exceptions\UserNotFound;
use Reflex\Lockdown\Exceptions\RoleNotFound;
use Closure;
use BadMethodCallException;

class Lockdown
{
    /**
     * User provider
     * @var Reflex\Lockdown\Users\ProviderInterface
     */
    protected $userProvider;

    /**
     * Role provider
     * @var Reflex\Lockdown\Roles\ProviderInterface
     */
    protected $roleProvider;

    /**
     * Permission provider
     * @var Reflex\Lockdown\Permissions\ProviderInterface
     */
    protected $permissionProvider;

    /**
     * Allowed levels for permissions
     * @var array
     */
    protected $allowedLevels  =   ['allow', 'deny'];

    /**
     * Constructor
     * @param  Reflex\Lockdown\Users\ProviderInterface       $user
     * @param  Reflex\Lockdown\Roles\ProviderInterface       $role
     * @param  Reflex\Lockdown\Permissions\ProviderInterface $permission
     * @return void
     */
    public function __construct(
        UserProviderInterface $user,
        RoleProviderInterface $role,
        PermissionProviderInterface $permission
    ) {
        $this->userProvider         =   $user;
        $this->roleProvider         =   $role;
        $this->permissionProvider   =   $permission;
    }

    /**
     * Get allowed role permission levels
     * @return array
     */
    public function getAllowedPermissionLevels()
    {
        return $this->allowedLevels;
    }

    /**
     * Check permission level
     * @param  string $level Level to check
     * 
     * @return boolean
     * 
     * @throws \Reflex\Lockdown\Exceptions\PermisionLevelNotAllowed 
     *         If permission level not satisfactory
     */
    public function checkPermissionLevel($level)
    {
        if (in_array($level, $this->getAllowedPermissionLevels())) {
            return true;
        }

        throw new PermissionLevelNotAllowed;
    }

    /**
     * Get a role
     * @param string|Reflex\Lockdown\Roles\RoleInterface $roleKey Role lookup
     * 
     * @return Reflex\Lockdown\Roles\RoleInterface
     * 
     * @throws Reflex\Lockdown\Exceptions\RoleNotFound If Role isn't found
     */
    public function findRoleByKey($roleKey)
    {
        if ($roleKey instanceof RoleInterface) {
            return $roleKey;
        }

        $result         =   $this->roleProvider->findByKey($roleKey);
        if ($result) {
            return $result;
        }

        $exceptionMessage   =   "The role '%(rolekey)s' cannot be found.";

        throw new RoleNotFound(isprintf($exceptionMessage, compact('roleKey')));
    }

    /**
     * Get permission
     * @param string|Reflex\Lockdown\Permissions\PermissionInterface $key
     * 
     * @return Reflex\Lockdown\Permissions\PermissionInterface
     * 
     * @throws Reflex\Lockdown\PermissionNotFoundException 
     *         If Permission isn't found
     */
    public function findPermissionByKey($key)
    {
        if ($key instanceof PermissionInterface) {
            return $key;
        }

        $result =   $this->permissionProvider->findByKey($key);
        if ($result) {
            return $result;
        }

        $exceptionMessage   =   "The permission '%(key)s' cannot be found.";

        throw new PermissionNotFound(isprintf($exceptionMessage, compact('key')));
    }

    /**
     * Find a user by their ID
     * @param  integer|Reflex\Lockdown\Users\UserInterface $id
     * @return Reflex\Lockdown\Users\UserInterface
     * @throws Reflex\Lockdown\UserNotFoundException If User isn't found
     */
    public function findUserById($id)
    {
        if ($id instanceof UserInterface) {
            return $id;
        }

        $result =   $this->userProvider->findById($id);
        if ($result) {
            return $result;
        }

        $exceptionMessage   =   "A user with the ID '%(id)s' cannot be found.";

        throw new UserNotFound(isprintf($exceptionMessage, compact('id')));
    }

    /**
     * Find a user by their login
     * @param  string|Reflex\Lockdown\Users\UserInterface $id
     * @return Reflex\Lockdown\Users\UserInterface
     * @throws UserNotFoundException If User not found
     */
    public function findUserByLogin($login)
    {
        if ($login instanceof UserInterface) {
            return $login;
        }

        $result =   $this->userProvider->findByLogin($login);
        if ($result) {
            return $result;
        }

        $exceptionMessage   =   "A user with the login '%(login)s' cannot be found.";

        throw new UserNotFound(isprintf($exceptionMessage, compact('login')));
    }

    /**
     * Get all roles
     * @return array
     */
    public function findAllRoles()
    {
        return $this->roleProvider->findAll();
    }

    /**
     * Get all permissions
     * @return array
     */
    public function findAllPermissions()
    {
        return $this->permissionProvider->findAll();
    }

    /**
     * Get all users
     * @return array
     */
    public function findAllUsers()
    {
        return $this->userProvider->findAll();
    }

    /**
     * Give a role a permission
     * @param  Reflex\Lockdown\Roles\RoleInterface|string             $role
     * @param  Reflex\Lockdown\Permissions\PermissionInterface|string $permission
     * @param  string                                                 $level
     * @return boolean
     */
    public function giveRolePermission($role, $permission, $level = 'allow')
    {
        $this->checkPermissionLevel($level);

        $role       =   $this->findRoleByKey($role);
        $permission =   $this->findPermissionByKey($permission);

        return $role->give($permission, $level);
    }

    /**
     * Remove a permission from a role
     * @param  Reflex\Lockdown\Roles\RoleInterface|string            $role
     * @param  Reflex\Lockdown\Permission\PermissionInterface|string $permission
     * @return boolean
     */
    public function removeRolePermission($role, $permission)
    {
        $role       =   $this->findRoleByKey($role);
        $permission =   $this->findPermissionByKey($permission);

        return $role->remove($permission);
    }

    /**
     * Give a user a permission
     * @param  Reflex\Lockdown\Users\UserInterface|string             $user
     * @param  Reflex\Lockdown\Permissions\PermissionInterface|string $permission
     * @param  string                                                 $level
     * @return boolean
     */
    public function giveUserPermission($user, $permission, $level = 'allow')
    {
        $this->checkPermissionLevel($level);

        $user           =   $this->findUserById($user);
        $permission     =   $this->findPermissionByKey($permission);

        return $user->give($permission, $level);
    }

    /**
     * Remove a permission from a user
     * @param  Reflex\Lockdown\Users\UserInterface|string             $user
     * @param  Reflex\Lockdown\Permissions\PermissionInterface|string $permission
     * @return boolean
     */
    public function removeUserPermission($user, $permission)
    {
        $permissions=   $this->findPermissionByKey($permission);
        $user       =   $this->findUserById($user);

        return $user->remove($permission);
    }

    /**
     * Give a user a role
     * @param  Reflex\Lockdown\Users\UserInterface|string $user
     * @param  Reflex\Lockdown\Roles\RoleInterface|string $role
     * @return boolean
     */
    public function giveUserRole($user, $role)
    {
        $user   =   $this->findUserById($user);
        $role   =   $this->findRoleByKey($role);

        if ($user->is($role->key)) {
            return true;
        }
        
        return $user->join($role);
    }

    /**
     * Remove a role from a user
     * @param  Reflex\Lockdown\Users\UserInterface|string $user
     * @param  Reflex\Lockdown\Roles\RoleInterface|string $role
     * @return boolean
     */
    public function removeUserRole($user, $role)
    {
        $user   =   $this->findUserById($user);
        $role   =   $this->findRoleByKey($role);

        if ($user->not($role->key)) {
            return true;
        }

        return $user->leave($role);
    }

    /**
     * Create a new role
     * @param  string $name
     * @param  string $key
     * @param  string $description
     * @return boolean
     */
    public function createRole($name, $key = null, $description = null)
    {
        $key        =   snake_case(isset($key) ? $key : $name);
        $description=   isset($description) ? $description : $name;
        $values     =   compact('name', 'key', 'description');

        return $this->roleProvider->create($values);
    }

    /**
     * Delete a role
     * @param  string $key
     * @return boolean
     */
    public function deleteRole($key)
    {
        return $this->roleProvider->delete($key);
    }

    /**
     * Create a permission
     * @param  string $name
     * @param  string $key
     * @param  string $description
     * @return boolean
     */
    public function createPermission($name, $key = null, $description = null)
    {
        $key        =   snake_case(isset($key) ? $key : $name);
        $description=   isset($description) ? $description : $name;
        $values     =   compact('name', 'key', 'description');

        return $this->permissionProvider->create($values);
    }

    /**
     * Delete a permission
     * @param  string $key
     * @return boolean
     */
    public function deletePermission($key)
    {
        return $this->permissionProvider->delete($key);
    }

    /**
     * Does the user have a permission?
     * @param  integer $id
     * @param  string  $permission
     * @param  boolean $all
     * @return boolean
     */
    public function has($id, $permission, $all = true)
    {
        if (! $user = $this->findUserById($id)) {
            return false;
        }

        return $user->has($permission, $all);
}

    /**
     * Does the user not have a permission
     * @param  integer $id
     * @param  string  $permission
     * @param  boolean $all
     * @return boolean
     */
    public function hasnt($id, $permission, $all = true)
    {
        return false === $this->has($id, $permission, $all);
    }

    /**
     * Is the user part of a role?
     * @param  integer $id
     * @param  string  $role
     * @param  boolean $all
     * @return boolean
     */
    public function is($id, $role, $all = true)
    {
        if (! $user = $this->findUserById($id)) {
            return false;
        }

        return $user->is($role, $all);
    }

    /**
     * Is the user not part of a role?
     * @param  integer $id
     * @param  string  $role
     * @param  boolean $all
     * @return boolean
     */
    public function not($id, $role, $all = true)
    {
        return false === $this->is($id, $role, $all);
    }

    /**
     * Dynamically segment a string for a nice elegent role/permission look up
     * @param  string  $finder
     * @param  boolean $all
     * @return array
     */
    protected function dynamicSegmenter($finder, $all)
    {
        $splitOn    =   true === $all
            ? 'And'
            : 'Or';
        $pattern    =   "/($splitOn)(?=[A-Z])/";
        $segments   =   preg_split(
            $pattern,
            $finder,
            -1,
            PREG_SPLIT_DELIM_CAPTURE
        );

        $segments   =   array_where(
            $segments, 
            function ($key, $segment) use ($splitOn) {
                return $segment !== $splitOn;
            }
        );

        $segments   =   array_map('str_slug', $segments, ['_']);

        // foreach ($segments as $segment) {
        //     if ($segment !== $splitOn) {
        //         $return[]   =   snake_case($segment);
        //     }
        // }

        return $segments;
    }

    /**
     * Build a dynamic permission/role lookup
     * @param  string  $method
     * @param  integer $id
     * @return boolean|null
     */
    protected function dynamicBuilder($method, $id)
    {
        $all    =   true;
        if (starts_with($method, 'is')) {
            $prefixLength   =   2;
            $call           =   'is';
        } elseif (starts_with($method, 'has')) {
            $prefixLength   =   3;
            $call           =   'has';
        } else {
            return null;
        }

        $finder =   substr($method, $prefixLength);

        // Allows us to specify any or all
        if (starts_with($finder, 'OneOf')) {
            $all    =   false;
            $finder =   substr($finder, 5);
        }

        // SEGMENT!
        $lookupArray    =   $this->dynamicSegmenter($finder, $all);
            
        // Call the lookup!
        return $this->$call($id, $lookupArray, $all);
    }

    /**
     * __call
     * @param  string $method
     * @param  array  $arguments
     * @return boolean
     * @throws \BadMethodCallException If method not found
     */
    public function __call($method, array $arguments = [])
    {
        $lockdown   =   $this;
        $userId     =   array_get($arguments, 0, null);

        if (! is_numeric($userId)) {
            return false;
        }

        // Time for some dynamic role/permission lookup
        $dynamic    =   $this->dynamicBuilder($method, $userId);
        if (is_bool($dynamic)) {
            return $dynamic;
        }

        $className  =   get_class($lockdown);

        throw new BadMethodCallException(
            "Call to undefined method {$className}::{$method}()"
        );
    }
}
