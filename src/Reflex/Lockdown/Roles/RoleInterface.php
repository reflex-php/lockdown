<?php
/**
 * Lockdown ACL
 *
 * PHP version 5.4
 *
 * @category Package
 * @package  Reflex
 * @author   Mike Shellard <contact@mikeshellard.me>
 * @license  http://mikeshellard.me/reflex/license MIT
 * @link     http://mikeshellard.me/reflex/lockdown
 */

namespace Reflex\Lockdown\Roles;

use Reflex\Lockdown\Permissions\PermissionInterface;

/**
 * Role interface
 * @category Package
 * @package  Reflex
 * @author   Mike Shellard <contact@mikeshellard.me>
 * @license  http://mikeshellard.me/reflex/license MIT
 * @link     http://mikeshellard.me/reflex/lockdown
 */
interface RoleInterface
{
   
    /**
     * Get users in this role
     * @return mixed
     */
    public function users();

    /**
     * Get permission in this role
     * @return mixed
     */
    public function permissions();

    /**
     * Get the actual permission
     * @param  string $permission Permission name/key
     * @return mixed
     */
    public function getPermission($permission);

    /**
     * Does this role have supplied permission
     * @param mixed   $has Permission lookup
     * @param boolean $all Check for all permissions
     * @return boolean
     */
    public function has($has, $all = true);

    /**
     * Does this role not have supplied permission
     * @param mixed   $has Permission lookup
     * @param boolean $all Check for all permissions
     * @return boolean
     */
    public function hasnt($has, $all = true);

    /**
     * Give role a permission
     * @param  \Reflex\Lockdown\Permissions\PermissionInterface $permission
     *         Permission instance
     * @param  string                                           $level
     *         Level
     * @return boolean
     */
    public function give(PermissionInterface $permission, $level = 'allow');

    /**
     * Remove a permission from this role
     * @param  \Reflex\Lockdown\Permisssions\PermissionInterface $permission
     *         Permission instance
     * @return boolean
     */
    public function remove(PermissionInterface $permission);

    /**
     * Delete this role
     * @return boolean
     */
    public function delete();
}
