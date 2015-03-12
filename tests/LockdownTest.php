<?php

use Mockery as m;

class LockdownTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }
    
    public function testFindRoleByKey()
    {
        list($cache, $user, $role, $perm)   =   $this->getMocks();

        $cache->shouldReceive('get')
            ->once()
            ->andReturn(true);

        $lockdown   =   m::mock(
            'Reflex\Lockdown\Lockdown[cache]',
            [
                $cache,
                $user,
                $role,
                $perm
            ]
        );

        $this->assertTrue($lockdown->findRoleByKey('foobar'));
    }

    public function testFindRoleByKeyFails()
    {
        $this->setExpectedException(
            'Reflex\Lockdown\RoleNotFoundException',
            "The role 'foobar' cannot be found."
        );

        list($cache, $user, $role, $perm)   =   $this->getMocks();

        $cache->shouldReceive('get')
            ->once()
            ->andReturn(false);
        $lockdown   =   m::mock(
            'Reflex\Lockdown\Lockdown[cache]',
            [
                $cache,
                $user,
                $role,
                $perm
            ]
        );

        $lockdown->findRoleByKey('foobar');
    }

    protected function getMocks()
    {
        return [
            m::mock('Reflex\Lockdown\LockdownCacheLayer'),
            m::mock('Reflex\Lockdown\Users\ProviderInterface'),
            m::mock('Reflex\Lockdown\Roles\ProviderInterface'),
            m::mock('Reflex\Lockdown\Permissions\ProviderInterface'),
        ];
    }
}