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

namespace Reflex\Lockdown\Roles\Eloquent;

use Reflex\Lockdown\Roles\ProviderInterface;

/**
 * Provider
 * @category Package
 * @package  Reflex
 * @author   Mike Shellard <contact@mikeshellard.me>
 * @license  http://mikeshellard.me/reflex/license MIT
 * @link     http://mikeshellard.me/reflex/lockdown
 */
class Provider implements ProviderInterface
{
    /**
     * Model class name
     * @var string
     */
    protected $model;

    /**
     * Constructor
     * @param string $model Model class name
     */
    public function __construct($model = null)
    {
        $this->model    =   $model;
    }

    /**
     * Create model instance
     * @return \Illuminate\Database\Eloquent\Model Model instance
     */
    protected function createModelInstance()
    {
        return new $this->model;
    }

    /**
     * Create a role
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
     * Delete a role
     * @param  string $key Lookup key
     * @return boolean
     */
    public function delete($key)
    {
        $role   =   $this->findByKey($key);
        if (! isset($role)) {
            return true;
        }

        return $role->delete();
    }

    /**
     * Find role by ID
     * @param  integer $id Role ID
     * @return mixed
     */
    public function findById($id)
    {
        $model  =   $this->model;
        return $model::find($id);
    }

    /**
     * Find role by name
     * @param  string $name Role name
     * @return mixed
     */
    public function findByName($name)
    {
        return with($this->createModelInstance())
            ->whereName($name)->first();
    }

    /**
     * Find role by key
     * @param  string $key Role key
     * @return mixed
     */
    public function findByKey($key)
    {
        return with($this->createModelInstance())
            ->whereKey($key)->first();
    }

    /**
     * Find all roles
     * @return mixed
     */
    public function findAll()
    {
        $model  =   $this->model;
        return $model::get()->all();
    }

    /**
     * Find roles with permission
     * @param  \Reflex\Lockdown\Permissions\PermissionInterface $permission
     *         Permission instance
     * @return mixed
     */
    public function findWithPermission($permission)
    {
        return array_filter(
            $this->findAll(),
            function ($role) use ($permission) {
                return $role->has($permission);
            }
        );
    }

    /**
     * Find roles without permission
     * @param  \Reflex\Lockdown\Permissions\PermissionInterface $permission
     *         Permission instance
     * @return mixed
     */
    public function findWithoutPermission($permission)
    {
        return array_filter(
            $this->findAll(),
            function ($role) use ($permission) {
                return $role->hasnt($permission);
            }
        );
    }
}
