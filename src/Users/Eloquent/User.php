<?php
namespace Reflex\Lockdown\Users\Eloquent;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Reflex\Lockdown\Permissions\PermissionInterface;
use Reflex\Lockdown\Roles\RoleInterface;
use Reflex\Lockdown\Users\UserInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

/**
 * User Model
 */
class User extends Model implements
    UserInterface,
    AuthenticatableContract,
    CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    /**
     * Local cache for permissions/roles
     * @var array
     */
    protected $cache            =   [];

    /**
     * Login lookup attribute
     * @var string
     */
    protected $loginAttribute   =   'email';

    /**
     * Login attribute mutator
     * @param string $attribute
     * @return void
     */
    public function setLoginAttribute($attribute)
    {
        $this->loginAttribute   =   $attribute;
    }

    /**
     * Login attribute mutator
     * @return string
     */
    public function getLoginAttribute()
    {
        return $this->loginAttribute;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    /**
     * Remember token mutator
     * @return string
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Remember token mutator
     * @param string $value
     * @return void
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * Remember token name mutator
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Retrieve all users roles
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(
            'Reflex\Lockdown\Roles\Eloquent\Role',
            'lockdown_user_roles',
            'user_id',
            'role_id'
        )->withTimestamps();
    }

    /**
     * Retrieve all users permissions
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->morphToMany(
            'Reflex\Lockdown\Permissions\Eloquent\Permission',
            'permissionable',
            'lockdown_permissionables'
        )->withPivot('level');
    }

    protected function roleFilter($key, $role)
    {
        // code...
    }

    /**
     * Is the user in the role(s)?
     * @param  string|array $roles
     * @param  boolean      $all
     * @return boolean
     */
    public function is($roles, $all = true)
    {
        if (! is_array($roles)) {
            $roles  =   (array) $roles;
        }

        $allRoles   =   $this->roles
            ->toArray();
        $filtered   =   array_where(
            $allRoles,
            function ($key, $role) use ($roles) {
                return in_array($role['key'], $roles);
            }
        );
        
        $filteredCount  =   count($filtered);
        // Are we looking for all the roles here?
        if (true === $all) {
            return count($roles) === $filteredCount;
        }

        // Not at all. Any is good enough
        return 0 < $filteredCount;
    }

    /**
     * Is the user not in the roles?
     * @param  string|array $roles Role name to lookup
     * @param  boolean      $all
     * @return boolean
     */
    public function not($roles, $all = true)
    {
        return false === $this->is($roles, $all);
    }

    protected function permissionFilter($key, $permission)
    {
        if (true === $this->getLocalCacheValue($permission)) {
            return true;
        }

        $permissionLookup   =   $this->getPermission($permission);
        $allowed            =   false;

        if (isset($permissionLookup)) {
            $allowed    =   $permissionLookup->isDenied()
                ? false : $permissionLookup->isAllowed();
        } elseif ($this->roles->has($permission)) {
            $allowed        =   true;
        }

        $this->addToLocalCache($permission, $allowed);

        return $allowed;
    }

    /**
     * Does the user have the permission?
     * @param  string|array $permissions Permission to lookup
     * @return boolean
     */
    public function has($permissions, $all = true)
    {
        if (! is_array($permissions)) {
            $permissions    =   (array) $permissions;
        }

        $permissions=   array_unique($permissions);
        $filtered   =   [];
        $usersRoles =   $this->roles;
        $allowed    =   false;
        $filtered   =   array_where($permissions, [$this, 'permissionFilter']);
        $filteredCount  =   count($filtered);

        // Does the permission lookup require all permissions to be found?
        if (true === $all) {
            return count($permissions) === $filteredCount;
        }

        // Nope, just as long as they have one of them
        return 0 < $filteredCount;
    }

    /**
     * Does the user not have the permission?
     * @param  string|array $permissions Permissions to lookup
     * @return boolean
     */
    public function hasnt($permissions, $all = true)
    {
        return false === $this->has($permissions, $all);
    }

    /**
     * Get the actual permission and level assigned to the user
     * @param  string|PermissionInterface $permission Permission name/key
     * @return Model|Builder|null
     */
    public function getPermission($permission)
    {
        if ($permission instanceof PermissionInterface) {
            $permission =   $permission->key;
        }

        return $this->permissions()
            ->key($permission)
            ->first();
    }

    /**
     * Give a permission to the user
     * @param  PermissionInterface $permission
     * @param  string              $level
     * @return boolean
     */
    public function give(PermissionInterface $permission, $level = 'allow')
    {
        $current    =   $this->getPermission($permission->key);
        if (isset($current)) {
            if ($current->level === $level) {
                return true;
            }
            $this->remove($permission);
        }

        $this->removeFromLocalCache($permission->key);

        $this->permissions()->attach($permission, compact('level'));

        return false === is_null($this->getPermission($permission));
    }

    /**
     * Remove a permission from the user
     * @param  PermissionInterface $permission
     * @return boolean
     */
    public function remove(PermissionInterface $permission)
    {
        $this->removeFromLocalCache($permission->key);

        if (! is_null($this->getPermission($permission))) {
            $this->permissions()
                ->detach($permission);
        }

        return true;
    }

    /**
     * Join a role
     * @param  RoleInterface $role
     * @return boolean
     */
    public function join(RoleInterface $role)
    {
        $this->roles()
            ->attach($role);

        return $this->is($role);
    }

    /**
     * Leave a role
     * @param  RoleInterface $role
     * @return boolean
     */
    public function leave(RoleInterface $role)
    {
        $this->roles()
            ->detach($role);

        return $this->isnt($role);
    }

    /**
     * Delete user and associated information
     * @return boolean
     */
    public function delete()
    {
        $this->roles()
            ->detach();
        $this->permissions()
            ->detach();

        return parent::delete();
    }

    /**
     * Remove item from local cache array
     * @param  string $key
     * @return boolean
     */
    protected function removeFromLocalCache($key)
    {
        unset($this->cache[ $key ]);

        return true;
    }

    /**
     * Add a value to local cache array
     * @param  string $key
     * @param  mixed  $value
     * @return boolean
     */
    protected function addToLocalCache($key, $value)
    {
        $this->cache[ $key ]    =   $value;

        return true;
    }

    /**
     * Get a value from locacl cache array
     * @param  string $key
     * @return mixed
     */
    protected function getLocalCacheValue($key)
    {
        return array_get($this->cache, $key, null);
    }
}
