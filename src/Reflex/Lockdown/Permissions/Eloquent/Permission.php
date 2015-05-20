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

use Reflex\Lockdown\Permissions\PermissionInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

/**
 * Permission
 * @category Package
 * @package  Reflex
 * @author   Mike Shellard <contact@mikeshellard.me>
 * @license  http://mikeshellard.me/reflex/license MIT
 * @link     http://mikeshellard.me/reflex/lockdown
 */
class Permission extends Model implements PermissionInterface
{
    /**
     * Permission table
     * @var string
     */
    protected $table    =   'lockdown_permissions';

    /**
     * Fillables
     * @var array
     */
    protected $fillable =   ['name', 'key', 'description'];

    /**
     * Get users that have this permission
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function users()
    {
        return $this->morphedByMany(
            'Reflex\Lockdown\Users\Eloquent\User',
            'permissionable',
            'lockdown_permissionables'
        )->withPivot('level');
    }

    /**
     * Get roles with this permission
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function roles()
    {
        return $this->morphedByMany(
            'Reflex\Lockdown\Roles\Eloquent\Role',
            'permissionable',
            'lockdown_permissionables'
        )->withPivot('level');
    }

    /**
     * Get the pivot level attribute
     * @return string
     */
    public function getLevelAttribute()
    {
        return $this->pivot->level;
    }

    /**
     * Is this permission allowed?
     * @return boolean
     */
    public function isAllowed()
    {
        return 'allow' === $this->level;
    }

    /**
     * Is this permission explicitly denied?
     * @return boolean
     */
    public function isDenied()
    {
        return 'deny' === $this->level;
    }

    /**
     * Look up by key
     * @param Illuminate\Database\Query\Builder $query Query building
     * @param string                            $key   Lookup key
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeKey(Illuminate\Database\Query\Builder $query, $key)
    {
        return $query->whereKey($key);
    }

    /**
     * Delete permission
     * @return boolean
     */
    public function delete()
    {
        $this->users()->detach();
        $this->roles()->detach();

        return parent::delete();
    }
}
