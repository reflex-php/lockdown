<?php
namespace Reflex\Lockdown\Roles;

use Reflex\Lockdown\Permissions\PermissionInterface;

interface RoleInterface
{
    public function users();

    public function permissions();

    public function has($has);

    public function hasnt($has);

    public function give(PermissionInterface $permission, $level = 'allow');

    public function remove(PermissionInterface $permission);

    public function delete();
}
