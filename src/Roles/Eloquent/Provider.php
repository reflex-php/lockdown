<?php
namespace Reflex\Lockdown\Roles\Eloquent;

use Reflex\Lockdown\Roles\ProviderInterface;

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
        $role   =   $this->findByKey($key);
        if (! isset($role)) {
            return true;
        }

        return $role->delete();
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

    public function findWithPermission($permission)
    {
        return array_filter(
            $this->findAll(),
            function ($role) use ($permission) {
                return $role->has($permission);
            }
        );
    }

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
