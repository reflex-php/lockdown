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
use Closure;

class Lockdown
{
    /**
     * Cache instance
     * @var Reflex\Lockdown\LockdownCacheLayer
     */
    protected $cache;

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
     * @param  Reflex\Lockdown\LockdownCacheLayer $cache 
     * @param  Reflex\Lockdown\Users\ProviderInterface $user 
     * @param  Reflex\Lockdown\Roles\ProviderInterface $role 
     * @param  Reflex\Lockdown\Permissions\ProviderInterface $permission 
     * @return void 
     */
    public function __construct(
        LockdownCacheLayer $cache,
        UserProviderInterface $user,
        RoleProviderInterface $role,
        PermissionProviderInterface $permission
    ) {
        $this->cache                =   $cache;
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
     * @param  string  $level 
     * @return boolean        
     * @throws PermisionLevelNotAllowedException If permission level not satisfactory
     */
    public function checkPermissionLevel($level)
    {
        if (in_array($level, $this->getAllowedPermissionLevels())) {
            return true;
        }

        throw new PermissionLevelNotAllowedException;
    }

    /**
     * Handle cache 
     * @param  Closure $callback 
     * @param  array   $miscData 
     * @return mixed            
     */
    protected function cache(Closure $callback, $miscData = [])
    {
        if (false === is_array($miscData)) {
            $miscData   =   (array) $miscData;
        }

        $miscData   =   $this->buildCacheMisc($miscData);
        return $this->cache->get($callback, $miscData);
    }

    /**
     * Build cache misc data
     * @param  array $data 
     * @return string       
     */
    protected function buildCacheMisc(array $data = [])
    {
        $data[] =   get_caller(get_caller());
        return sha1(base64_encode(json_encode($data)));
    }

    /**
     * Get a role
     * @param  string|Reflex\Lockdown\Roles\RoleInterface $key 
     * @return Reflex\Lockdown\Roles\RoleInterface    
     * @throws RoleNotFoundException If Role isn't found 
     */
    public function findRoleByKey($key)
    {
        if ($key instanceof RoleInterface) {
            return $key;
        }

        $roleProvider   =   $this->roleProvider;

        $result =   $this->cache(
            function () use ($key, $roleProvider) {
                return $roleProvider->findByKey($key);
            },
            $key
        );

        if ($result) {
            return $result;
        }

        throw new RoleNotFoundException;
    }

    /**
     * Get permission
     * @param  string|Reflex\Lockdown\Permissions\PermissionInterface $key 
     * @return Reflex\Lockdown\Permissions\PermissionInterface   
     * @throws PermissionNotFoundException If Permission isn't found   
     */
    public function findPermissionByKey($key)
    {
        if ($key instanceof PermissionInterface) {
            return $key;
        }

        $result =   $this->cache(
            function () use ($key) {
                return $this->permissionProvider->findByKey($key);
            },
            $key
        );

        if ($result) {
            return $result;
        }

        throw new PermissionNotFoundException;
    }

    /**
     * Find a user by their ID
     * @param  integer|Reflex\Lockdown\Users\UserInterface $id 
     * @return Reflex\Lockdown\Users\UserInterface
     * @throws UserNotFoundException If User isn't found
     */
    public function findUserById($id)
    {
        if ($id instanceof UserInterface) {
            return $id;
        }

        $result =   $this->cache(
            function () use ($id) {
                return $this->userProvider
                    ->findById($id);
            },
            $id
        );

        if ($result) {
            return $result;
        }

        throw new UserNotFoundException;
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

        $result =   $this->cache(
            function () use ($login) {
                return $this->userProvider
                    ->findByLogin($login);
            },
            $login
        );

        if ($result) {
            return $result;
        }

        throw new UserNotFoundException;
    }

    /**
     * Get all roles
     * @return array
     */
    public function findAllRoles()
    {
        return $this->cache(
            function () {
                return $this->roleProvider
                    ->findAll();
            }
        );
    }

    /**
     * Get all permissions
     * @return array 
     */
    public function findAllPermissions()
    {
        return $this->cache(
            function () {
                return $this->permissionProvider
                    ->findAll();
            }
        );
    }

    /**
     * Get all users
     * @return array 
     */
    public function findAllUsers()
    {
        return $this->cache(
            function () {
                return $this->userProvider
                    ->findAll();
            }
        );
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
     * @param  string  $name        
     * @param  string  $key         
     * @param  string  $description 
     * @return boolean              
     */
    public function createRole($name, $key = null, $description = null)
    {
        return $this->roleProvider
            ->create(
            [
                'name'          =>  $name,
                'key'           =>  isset($key)
                    ? $key
                    : snake_case($name),
                'description'   =>  isset($description)
                    ? $description
                    : $name,
            ]
        );
    }

    /**
     * Delete a role
     * @param  string  $key 
     * @return boolean      
     */
    public function deleteRole($key)
    {
        return $this->roleProvider
            ->delete($key);
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
        return $this->permissionProvider
            ->create(
            [
                'name'          =>  $name,
                'key'           =>  isset($key)
                    ? $key
                    : snake_case($name),
                'description'   =>  isset($description)
                    ? $description
                    : $name,
            ]
        );
    }

    /**
     * Delete a permission
     * @param  string  $key 
     * @return boolean      
     */
    public function deletePermission($key)
    {
        return $this->permissionProvider
            ->delete($key);
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
        if (! $user   =   $this->findUserById($id)) {
            return false;
        }

        $arguments  =   func_get_args();

        return $this->cache(
            function () use ($user, $permission, $all) {
                return $user->has($permission, $all);
            },
            $arguments
        );
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
        $user   =   $this->findUserById($id);
        if (! $user) {
            return false;
        }

        $arguments  =   func_get_args();

        return $this->cache(
            function () use ($user, $role, $all) {
                return $user->is($role, $all);
            },
            $arguments
        );
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
        $return     =   [];

        foreach ($segments as $segment) {
            if ($segment !== $splitOn) {
                $return[]   =   snake_case($segment);
            }
        }

        return $return;
    }

    /**
     * Build a dynamic permission/role lookup
     * @param  string  $method 
     * @param  integer $id     
     * @return boolean|null         
     */
    protected function dynamicBuilder($method, $id)
    {

        return $this->cache(
            function () use ($method, $id) {
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
            },
            func_get_args()
        );
    }

    /**
     * __call
     * @param  string  $method    
     * @param  array   $arguments 
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

        throw new \BadMethodCallException(
            "Call to undefined method {$className}::{$method}()"
        );
    }
}
