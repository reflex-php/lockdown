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

namespace Reflex\Lockdown\Tests;

use Mockery as m;
use Faker\Factory as Faker;
use stdClass;

/**
 * LockdownTest
 * @category Package
 * @package  Reflex
 * @author   Mike Shellard <contact@mikeshellard.me>
 * @license  http://mikeshellard.me/reflex/license MIT
 * @link     http://mikeshellard.me/reflex/lockdown
 */
class LockdownTest extends TestCase
{
    

    public function testLockdown()
    {
        $roleKey    =   'admin';
        $role       =   m::mock('Reflex\Lockdown\Roles\RoleInterface');
        $user       =   m::mock('Reflex\Lockdown\Users\UserInterface');
        $user->shouldReceive('has')
            ->with($roleKey, true)
            ->andReturn(true);

        $userProvider       =   $this->createMockUserProvider();
        $userProvider->shouldReceive('findById')    
            ->with(1)
            ->andReturn($user);

        $roleProvider       =   $this->createMockRoleProvider();
        $roleProvider->shouldReceive('findByKey')
            ->with($roleKey)
            ->andReturn($role);

        $permissionProvider =   $this->createMockPermissionProvider();
        $lockdown   =   new \Reflex\Lockdown\Lockdown(
            $userProvider,
            $roleProvider,
            $permissionProvider
        );

        $this->assertTrue($lockdown->has(1, $roleKey));
    }
}