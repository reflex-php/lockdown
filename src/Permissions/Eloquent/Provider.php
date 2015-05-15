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

namespace Reflex\Lockdown\Permissions\Eloquent;

use Reflex\Lockdown\Permissions\ProviderInterface;
use Reflex\Lockdown\Roles\RoleInterface;

/**
 * Permission provider
 * @category Package
 * @package  Reflex
 * @author   Mike Shellard <contact@mikeshellard.me>
 * @license  http://mikeshellard.me/reflex/license MIT
 * @link     http://mikeshellard.me/reflex/lockdown
 */
class Provider implements ProviderInterface
{
    /**
     * Model string
     * @var string
     */
    protected $model;

    /**
     * Construct
     * @param string $model Model
     */
    public function __construct($model = null)
    {
        $this->model    =   $model;
    }

    /**
     * Create model instance
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function createModelInstance()
    {
        return new $this->model;
    }

    /**
     * Create a permission
     * @param array $attributes Attributes
     * @return boolean
     */
    public function create(array $attributes)
    {
        return with($this->createModelInstance())
            ->fill($attributes)
            ->save();
    }

    /**
     * Delete a permission
     * @param  string $key Lookup key
     * @return boolean
     */
    public function delete($key)
    {
        $permission   =   $this->findByKey($key);
        if (! isset($permission)) {
            return true;
        }

        return $permission->delete();
    }

    /**
     * Find a permission by ID
     * @param  integer $id Permission ID
     * @return \Reflex\Permissions\PermissionInterface
     */
    public function findById($id)
    {
        $model  =   $this->model;
        return $model::find($id);
    }

    /**
     * Find permission by name
     * @param  string $name Permission name
     * @return \Reflex\Permissions\PermissionInterface
     */
    public function findByName($name)
    {
        return with($this->createModelInstance())
            ->whereName($name)
            ->first();
    }

    /**
     * Find permission by key
     * @param  string $key Permission key
     * @return \Reflex\Permissions\PermissionInterface
     */
    public function findByKey($key)
    {
        return with($this->createModelInstance())
            ->whereKey($key)
            ->first();
    }

    /**
     * Find all permissions
     * @return \Illuminate\Support\Collection
     */
    public function findAll()
    {
        $model  =   $this->model;
        return $model::get()->all();
    }

    /**
     * Find permissions in a role
     * @param  Reflex\Lockdown\Roles\RoleInterface $role Role instance
     * @return \Illuminate\Support\Collection
     */
    public function findInRole(RoleInterface $role)
    {
        return $role->permissions()->get();
    }

    /**
     * Find permission not in a role
     * @param  Reflex\Lockdown\Roles\RoleInterface $role Role instance
     * @return \Illuminate\Support\Collection
     */
    public function findNotInRole(RoleInterface $role)
    {
        $callback   =   function ($permission) use ($role) {
            return $role->hasnt($permission->key);
        }
        return array_filter($this->findAll(), $callback);
    }
}
