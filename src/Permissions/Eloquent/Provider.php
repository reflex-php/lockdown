<?php
namespace Reflex\Lockdown\Permissions\Eloquent;

use Reflex\Lockdown\Permissions\ProviderInterface;
use Reflex\Lockdown\Roles\RoleInterface;

class Provider implements ProviderInterface
{
    protected $model;

    public function __construct($model = null)
    {
        $this->model    =   $model;
    }

    protected function createModelInstance()
    {
        return new $this->model;
    }

    public function create(array $attributes)
    {
        return with($this->createModelInstance())
            ->fill($attributes)
            ->save();
    }

    public function delete($key)
    {
        $permission   =   $this->findByKey($key);
        if (! isset($permission)) {
            return true;
        }

        return $permission->delete();
    }

    public function findById($id)
    {
        $model  =   $this->model;
        return $model::find($id);
    }

    public function findByName($name)
    {
        return with($this->createModelInstance())
            ->whereName($name)->first();
    }

    public function findByKey($key)
    {
        return with($this->createModelInstance())
            ->whereKey($key)->first();
    }

    public function findAll()
    {
        $model  =   $this->model;
        return $model::get()->all();
    }

    public function findInRole(RoleInterface $role)
    {
        return $role->permissions()->get();
    }

    public function findNotInRole(RoleInterface $role)
    {
        return array_filter(
            $this->findAll(),
            function ($permission) use ($role) {
                return $role->hasnt($permission->key);
            }
        );
    }
}
