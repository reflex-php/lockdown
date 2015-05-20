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
     * Create a role
     * @param array $attributes Attributes
     * @return boolean
     */
    public function create(array $attributes);

    /**
     * Delete a role
     * @param string $key Lookup key
     * @return boolean
     */
    public function delete($key);

    /**
     * Find role by ID
     * @param integer $id Role ID
     * @return mixed
     */
    public function findById($id);

    /**
     * Find role by name
     * @param string $name Role name
     * @return mixed
     */
    public function findByName($name);

    /**
     * Find role by key
     * @param string $key Role key
     * @return mixed
     */
    public function findByKey($key);

    /**
     * Find all roles
     * @return mixed
     */
    public function findAll();

    /**
     * Find roles with permission
     * @param  \Reflex\Lockdown\Permissions\PermissionInterface $permission
     *         Permission instance
     * @return mixed
     */
    public function findWithPermission($permission);

    /**
     * Find roles without permission
     * @param  \Reflex\Lockdown\Permissions\PermissionInterface $permission
     *         Permission instance
     * @return mixed
     */
    public function findWithoutPermission($permission);
}
