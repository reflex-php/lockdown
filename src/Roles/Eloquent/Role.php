<?php
namespace Reflex\Lockdown\Roles\Eloquent;

use Reflex\Lockdown\Permissions\PermissionInterface;
use Reflex\Lockdown\Roles\RoleInterface;
use Illuminate\Database\Eloquent\Model;

class Role extends Model implements RoleInterface
{
    protected $table    =   'lockdown_roles';

    protected $fillable =   ['name', 'key', 'description'];

    protected $cache    =   [];

    public function users()
    {
        return $this->belongsToMany(
            'Reflex\Lockdown\Users\Eloquent\User',
            'lockdown_user_roles',
            'role_id',
            'user_id'
        )->withTimestamps();
    }

    public function permissions()
    {
        return $this->morphToMany(
            'Reflex\Lockdown\Permissions\Eloquent\Permission',
            'permissionable',
            'lockdown_permissionables'
        )->withPivot('level');
    }

    /**
     * Get the actual permission
     * @param  string $permission Permission name/key
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

    public function has($permissions, $all = true)
    {
        if (! is_array($permissions)) {
            $permissions    =   (array) $permissions;
        }

        $permissions=   array_unique($permissions);
        $cache      =   &$this->cache;
        $filtered   =   array_where(
            $permissions,
            function ($permission) use ($cache) {
                if (array_key_exists($permission, $cache) && true === $cache[ $permission ]) {
                    return true;
                }

                if ($permissionResult = $this->getPermission($permission)) {
                    if ($permissionResult->isDenied()) {
                        return $cache[ $permission ]   =   false;
                    }

                    return $cache[ $permission ]   =   $permissionResult->isAllowed();
                }

                return $cache[ $permission ]    =   false;
            }
        );

        if (true === $all) {
            return count($permissions) === count($filtered);
        }

        return 0 < count($filtered);
    }

    public function hasnt($has, $all = true)
    {
        return false === $this->has($has, $all);
    }

    public function give(PermissionInterface $permission, $level = 'allow')
    {   
        $current    =   $this->getPermission($permission->key);
        if (isset($current)) {
            if ($current->level === $level) {
                return true;
            }
            $this->remove($permission);
        }

        unset($this->cache[ $permission->key ]);
        $this->permissions()
            ->attach($permission, ['level' => $level]);

        return ! is_null($this->getPermission($permission));
    }

    public function remove(PermissionInterface $permission)
    {
        unset($this->cache[ $permission->key ]);

        if (! is_null($this->getPermission($permission))) {
            $this->permissions()
                ->detach($permission);
        }

        return true;
    }

    public function delete()
    {
        $this->users()->detach();
        $this->permissions()->detach();

        return parent::delete();
    }
}
