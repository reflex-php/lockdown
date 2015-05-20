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

return [
    // User model
    'user'      =>  'Reflex\Lockdown\Users\Eloquent\User',

    // Role model
    'role'      =>  'Reflex\Lockdown\Roles\Eloquent\Role',

    // Permission model
    'permission'=>  'Reflex\Lockdown\Permissions\Eloquent\Permission',
];
