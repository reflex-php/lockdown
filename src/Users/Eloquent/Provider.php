<?php
namespace Reflex\Lockdown\Users\Eloquent;

use Reflex\Lockdown\Roles\RoleInterface;
use Reflex\Lockdown\Users\ProviderInterface;

class Provider implements ProviderInterface
{
    /**
     * Model to be provided
     * @var string
     */
    protected $model;

    /**
     * Constructor
     * @param string $model 
     * @return void 
     */
    public function __construct($model = null)
    {
        $this->model    =   $model;
    }

    /**
     * Create instance of the model
     * @return Reflex\Lockdown\Roles\UserInterface 
     */
    protected function createModelInstance()
    {
        return new $this->model;
    }

    /**
     * Find role by ID
     * @param  integer $id        
     * @return Reflex\Lockdown\Roles\UserInterface     
     */
    public function findById($id)
    {
        $model  =   $this->model;
        return $model::find($id);
    }

    /**
     * Find user by login
     * @param  string $login 
     * @return Reflex\Lockdown\Users\UserInterface        
     */
    public function findByLogin($login)
    {
        $model  =   $this->createModelInstance();
        return $model->where(
                $model->getLoginAttribute(),
                $login
            )
            ->first();
    }

    /**
     * Get all users
     * @return array 
     */
    public function findAll()
    {
        $model  =   $this->model;
        return $model::get()->all();
    }

    /**
     * Find users with permission
     * @param  string|Reflex\Lockdown\Permissions\PermissionInterface $permission 
     * @return array             
     */
    public function findWithPermission($permission)
    {
        return array_filter(
            $this->findAll(),
            function ($user) use ($permission) {
                return $user->has($permission);
            }
        );
    }

    /**
     * Find users without permission
     * @param  string|Reflex\Lockdown\Permissions\PermissionInterface $permission 
     * @return array             
     */
    public function findWithoutPermission($permission)
    {
        return array_filter(
            $this->findAll(),
            function ($user) use ($permission) {
                return $user->hasnt($permission);
            }
        );
    }

    /**
     * Find users in role
     * @param  Reflex\Lockdown\Roles\RoleInterface $role 
     * @return array              
     */
    public function findInRole(RoleInterface $role)
    {
        return $role->users()
            ->get();
    }

    /**
     * Find users not in role
     * @param  Reflex\Lockdown\Roles\RoleInterface $role 
     * @return array              
     */
    public function findNotInRole(RoleInterface $role)
    {
        return array_filter(
            $this->findAll(),
            function ($user) use ($role) {
                return $user->not($role->key);
            }
        );
    }
}
