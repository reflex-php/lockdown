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
 * RoleTraits
 * @category Package
 * @package  Reflex
 * @author   Mike Shellard <contact@mikeshellard.me>
 * @license  http://mikeshellard.me/reflex/license MIT
 * @link     http://mikeshellard.me/reflex/lockdown
 */
trait RoleTraits
{
    /**
     * Local cache
     * @var array
     */
    protected $cache    =   [];

    /**
     * Set/get values from the local cache
     * @param string $key   Lookup key
     * @param mixed  $value Value to store
     * @return \Reflex\Roles\RoleTraits
     */
    protected function cache($key, $value = null)
    {
        if (is_null($value) && $this->cacheHas($key)) {
            array_forget($this->cache, $key);
            return null;
        }

        if (is_null($value)) {
            return array_get($this->cache, $key, false);
        }

        if (! is_bool($value)) {
            $value  =   false;
        }

        array_set($this->cache, $key, $value);

        return $value;
    }

    /**
     * Cache has key
     * @param  string $key Lookup key
     * @return boolean
     */
    protected function cacheHas($key)
    {
        return array_has($this->cache, $key);
    }

    /**
     * Has filter
     * @param string $permission Permission lookup key
     * @return boolean
     */
    protected function hasFilter($permission)
    {
        if ($this->cacheHas($permission)
            && true === $this->cache($permission)
        ) {
            return true;
        }

        $permissionResult   =   $this->getPermission($permission);

        if ($permissionResult) {
            return $this->cache($permission, $permissionResult->isAllowed());
        }

        return $this->cache($permission, false);
    }

    /**
     * Does this role have supplied permission
     * @param mixed   $permissions Permission lookup
     * @param boolean $all         Check for all permissions
     * @return boolean
     */
    public function has($permissions, $all = true)
    {
        if (! is_array($permissions)) {
            $permissions    =   (array) $permissions;
        }

        $permissions=   array_unique($permissions);
        $filtered   =   array_where($permissions, [$this, 'hasFilter']);

        if (true === $all) {
            return count($permissions) === count($filtered);
        }

        return 0 < count($filtered);
    }

    /**
     * Does this role not have supplied permission
     * @param mixed   $has Permission lookup
     * @param boolean $all Check for all
     * @return boolean
     */
    public function hasnt($has, $all = true)
    {
        return false === $this->has($has, $all);
    }
}
