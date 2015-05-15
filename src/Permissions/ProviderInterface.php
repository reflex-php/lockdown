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

namespace Reflex\Lockdown\Permissions;

use Reflex\Lockdown\Roles\RoleInterface;

/**
 * Provider interface
 * @category Package
 * @package  Reflex
 * @author   Mike Shellard <contact@mikeshellard.me>
 * @license  http://mikeshellard.me/reflex/license MIT
 * @link     http://mikeshellard.me/reflex/lockdown
 */
interface ProviderInterface
{
    /**
     * Create a permission
     * @param array $attributes Attributes
     * @return boolean
     */
    public function create(array $attributes);

    /**
     * Delete a permission
     * @param  string $key Lookup key
     * @return boolean
     */
    public function delete($key);

    /**
     * Find a permission by ID
     * @param  integer $id Permission ID
     * @return \Reflex\Permissions\PermissionInterface
     */
    public function findById($id);

    /**
     * Find permission by name
     * @param  string $name Permission name
     * @return \Reflex\Permissions\PermissionInterface
     */
    public function findByName($name);

    /**
     * Find permission by key
     * @param  string $key Permission key
     * @return \Reflex\Permissions\PermissionInterface
     */
    public function findByKey($key);

    /**
     * Find all permissions
     * @return \Illuminate\Support\Collection
     */
    public function findAll();

    /**
     * Find permissions in a role
     * @param  Reflex\Lockdown\Roles\RoleInterface $role Role instance
     * @return mixed
     */
    public function findInRole(RoleInterface $role);

    /**
     * Find permission not in a role
     * @param  Reflex\Lockdown\Roles\RoleInterface $role Role instance
     * @return mixed
     */
    public function findNotInRole(RoleInterface $role);
}
