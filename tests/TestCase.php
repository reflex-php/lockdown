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
use PHPUnit_Framework_TestCase;

/**
 * TestCase
 * @category Package
 * @package  Reflex
 * @author   Mike Shellard <contact@mikeshellard.me>
 * @license  http://mikeshellard.me/reflex/license MIT
 * @link     http://mikeshellard.me/reflex/lockdown
 */
abstract class TestCase extends PHPUnit_Framework_TestCase
{
    protected $roleProviderStubClassName    =   
        '\Reflex\Lockdown\Tests\Stubs\RoleProvider';

    protected $permProviderStubClassName    =
        '\Reflex\Lockdown\Tests\Stubs\PermissionProvider';

    protected $userProviderStubClassName    =
        '\Reflex\Lockdown\Tests\Stubs\UserProvider';

    public function tearDown()
    {
        m::close();
    }

    protected function getMethodsExcept($class, $methods = [])
    {
        return '[' . implode(
            ',',
            array_diff(
                get_class_methods($class),
                (array) $methods
            )
        ) . ']';
    }

    protected function createMockPartial($classname, array $params = [])
    {
        return m::mock($classname, $params)->makePartial();
    }

    protected function createMockRoleProvider()
    {
        return $this->createMockPartial($this->roleProviderStubClassName);
    }

    protected function createMockPermissionProvider()
    {
        return $this->createMockPartial($this->permProviderStubClassName);
    }

    protected function createMockUserProvider()
    {
        return $this->createMockPartial($this->userProviderStubClassName);
    }
}