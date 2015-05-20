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

/**
 * Permission interface
 * @category Package
 * @package  Reflex
 * @author   Mike Shellard <contact@mikeshellard.me>
 * @license  http://mikeshellard.me/reflex/license MIT
 * @link     http://mikeshellard.me/reflex/lockdown
 */
interface PermissionInterface
{
    /**
     * Get users that have this permission
     * @return mixed
     */
    public function users();

    /**
     * Get roles with this permission
     * @return mixed
     */
    public function roles();

    /**
     * Is this permission allowed
     * @return boolean
     */
    public function isAllowed();

    /**
     * Is this permission explicitly denied?
     * @return boolean
     */
    public function isDenied();

    /**
     * Delete permission
     * @return boolean
     */
    public function delete();
}
