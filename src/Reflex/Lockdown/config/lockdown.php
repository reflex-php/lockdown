<?php
/**
 * Lockdown ACL
 *
 * @category Package
 * @package  Reflex
 * @author   Mike Shellard <contact@mikeshellard.me>
 * @license  http://mikeshellard.me/reflex/license MIT
 * @link     http://mikeshellard.me/reflex/lockdown
 */

return [
    // User model
    'user'      =>  Reflex\Lockdown\Users\Eloquent\User::class,

    // Role model
    'role'      =>  Reflex\Lockdown\Roles\Eloquent\Role::class,

    // Permission model
    'permission'=>  Reflex\Lockdown\Permissions\Eloquent\Permission::class,
];
