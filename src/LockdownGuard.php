<?php
/**
 * 
 */

namespace Reflex\Lockdown;

use Illuminate\Auth\Guard as IlluminatedGuard;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Session\Store as SessionStore;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Traits\MacroableTrait;

/**
 * LockdownGuard which extends Auth
 */
class LockdownGuard extends IlluminatedGuard
{
    /**
     * User's roles
     * @var array
     */
    protected $roles        =   [];

    /**
     * User's permissions
     * @var array
     */
    protected $permissions  =   [];

    /**
     * Lockdown instance
     * @var Reflex\Lockdown\Lockdown
     */
    protected $lockdown;

    /**
     * Create a new authentication guard.
     *
     * @param  Reflex\Lockdown\Lockdown                   $lockdown
     * @param  \Illuminate\Contracts\Auth\UserProvider    $provider
     * @param  \Illuminate\Session\Store                  $session
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @return void
     */
    public function __construct(
        Lockdown $lockdown,
        UserProvider $provider,
        SessionStore $session,
        Request $request = null
    ) {
        $this->setLockdown($lockdown);
        parent::__construct($provider, $session, $request);
    }

    /**
     * Get Lockdown instance
     * @return Reflex\Lockdown\Lockdown Lockdown instance
     */
    public function getLockdown()
    {
        return $this->lockdown;
    }

    /**
     * Set Lockdown instance
     * @param  Reflex\Lockdown\Lockdown $lockdown Lockdown instance
     * @return void
     */
    public function setLockdown(Lockdown $lockdown)
    {
        $this->lockdown =   $lockdown;
    }

    /**
     * Is the user a member of this role?
     * @param  string|array $role Role name
     * @param  boolean      $all  Test for all roles
     * @return boolean       
     */
    public function is($role, $all = true)
    {
        if (! $this->check()) {
            return false;
        }

        return $this->lockdown
            ->is($this->getUserId(), $role, $all);
    }

    /**
     * Does the user have this permission?
     * @param  string|array $permission Permission name
     * @param  boolean      $all        Test for all permissions
     * @return boolean             
     */
    public function has($permission, $all = true)
    {
        if (! $this->check()) {
            return false;
        }

        return $this->lockdown
            ->has($this->getUserId(), $permission, $all);
    }

    public function getUserId()
    {
        return ! $this->check()
            ? null
            : $this->user()
                ->getAuthIdentifier();
    }

    /**
     * Call
     * @param  string $method    
     * @param  array  $arguments 
     * @return return boolean            
     */
    public function __call($method, array $arguments = [])
    {
        if (! $this->check()) {
            return false;
        }

        return call_user_func([$this->lockdown, $method], $this->getUserId());
    }
}
