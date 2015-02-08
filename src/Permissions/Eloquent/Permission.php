<?php
namespace Reflex\Lockdown\Permissions\Eloquent;

use Reflex\Lockdown\Permissions\PermissionInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

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
     * Get users
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany 
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
     * Get roles
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany 
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
     * Is the permission allowed
     * @return boolean 
     */
    public function isAllowed()
    {
        return 'allow' === $this->level;
    }

    /**
     * Is the permission denied
     * @return boolean 
     */
    public function isDenied()
    {
        return 'deny' === $this->level;
    }

    /**
     * Look up by key
     * @param  Illuminate\Database\Query\Builder $query
     * @param  mixed                             $key   
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeKey(Illuminate\Database\Query\Builder $query, $key)
    {
        return $query->whereKey($key);
    }

    /**
     * Delete permission and associated data
     * @return boolean 
     */
    public function delete()
    {
        $this->users()->detach();
        $this->roles()->detach();

        return parent::delete();
    }
}
