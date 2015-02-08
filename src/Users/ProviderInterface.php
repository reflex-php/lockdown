<?php
namespace Reflex\Lockdown\Users;

use Reflex\Lockdown\Roles\RoleInterface;

interface ProviderInterface
{
    /**
     * Find role by ID
     * @param  integer $id        
     * @return Reflex\Lockdown\Roles\UserInterface     
     */
    public function findById($id);

    /**
     * Find user by login
     * @param  string $login 
     * @return Reflex\Lockdown\Users\UserInterface        
     */
    public function findByLogin($login);

    /**
     * Get all users
     * @return array 
     */
    public function findAll();

    /**
     * Find users with permission
     * @param  string|Reflex\Lockdown\Permissions\PermissionInterface $permission 
     * @return array             
     */
    public function findWithPermission($permission);

    /**
     * Find users without permission
     * @param  string|Reflex\Lockdown\Permissions\PermissionInterface $permission 
     * @return array             
     */
    public function findWithoutPermission($permission);

    /**
     * Find users in role
     * @param  Reflex\Lockdown\Roles\RoleInterface $role 
     * @return array              
     */
    public function findInRole(RoleInterface $role);

    /**
     * Find users not in role
     * @param  Reflex\Lockdown\Roles\RoleInterface $role 
     * @return array              
     */
    public function findNotInRole(RoleInterface $role);
}
