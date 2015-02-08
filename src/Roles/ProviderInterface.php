<?php
namespace Reflex\Lockdown\Roles;

interface ProviderInterface
{
    public function create(array $attributes);

    public function delete($key);

    public function findById($id);

    public function findByName($name);

    public function findByKey($key);

    public function findAll();

    public function findWithPermission($permission);

    public function findWithoutPermission($permission);
}
