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

use Reflex\Lockdown\Permissions\PermissionInterface;
use Reflex\Lockdown\Roles\RoleInterface;
use Reflex\Lockdown\Roles\RoleTraits;
use Illuminate\Database\Eloquent\Model;

/**
 * Role model
 * @category Package
 * @package  Reflex
 * @author   Mike Shellard <contact@mikeshellard.me>
 * @license  http://mikeshellard.me/reflex/license MIT
 * @link     http://mikeshellard.me/reflex/lockdown
 */
class Role extends Model implements RoleInterface
{
    use RoleTraits;
    
    /**
     * Table name
     * @var string
     */
    protected $table    =   'lockdown_roles';

    /**
     * Fillable values
     * @var array
     */
    protected $fillable =   ['name', 'key', 'description'];

    /**
     * Get users in this role
     * @return mixed
     */
    public function users()
    {
        return $this->belongsToMany(
            'Reflex\Lockdown\Users\Eloquent\User',
            'lockdown_user_roles',
            'role_id',
            'user_id'
        )->withTimestamps();
    }

    /**
     * Get permission in this role
     * @return mixed
     */
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

    /**
     * Give role a permission
     * @param  \Reflex\Lockdown\Permissions\PermissionInterface $permission
     *         Permission instance
     * @param  string                                           $level
     *         Level
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
        $this->cache($permission->key, null);
        $this->permissions()
            ->attach($permission, compact('level'));

        return ! is_null($this->getPermission($permission));
    }

    /**
     * Remove a permission from this role
     * @param  \Reflex\Lockdown\Permisssions\PermissionInterface $permission
     *         Permission instance
     * @return boolean
     */
    public function remove(PermissionInterface $permission)
    {
        $this->cache($permission->key, null);

        if (! is_null($this->getPermission($permission))) {
            $this->permissions()
                ->detach($permission);
        }

        return true;
    }

    /**
     * Delete this role
     * @return boolean
     */
    public function delete()
    {
        $this->users()->detach();
        $this->permissions()->detach();

        return parent::delete();
    }
}
