<?php
namespace Reflex\Lockdown;

use Illuminate\Support\Facades\Facade;

class LockdownFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'lockdown';
    }
}
