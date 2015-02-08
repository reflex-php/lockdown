<?php
namespace Reflex\Lockdown\Permissions;

interface PermissionInterface
{
    public function users();

    public function roles();

    public function isAllowed();

    public function isDenied();

    public function delete();
}
