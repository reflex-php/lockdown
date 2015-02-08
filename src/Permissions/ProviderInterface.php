<?php
namespace Reflex\Lockdown\Permissions;

use Reflex\Lockdown\Roles\RoleInterface;

interface ProviderInterface
{
    public function create(array $attributes);

    public function delete($key);

    public function findById($id);

    public function findByName($name);

    public function findByKey($key);

    public function findAll();

    public function findInRole(RoleInterface $role);

    public function findNotInRole(RoleInterface $role);
}
