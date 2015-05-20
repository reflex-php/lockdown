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


namespace Reflex\Lockdown\Commands;

use Reflex\Lockdown\Exceptions\PermissionNotFound;

/**
 * Create a new permission
 * @category Package
 * @package  Reflex
 * @author   Mike Shellard <contact@mikeshellard.me>
 * @license  http://mikeshellard.me/reflex/license MIT
 * @link     http://mikeshellard.me/reflex/lockdown
 */
class CreatePermission extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name         =   'lockdown:create-perm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description  =   'Create a new permission';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $permissionName =   $this->argument('permission');
        $description    =   $this->argument('description');
        $permissionKey  =   snake_case($permissionName);
        $lockdown       =   $this->lockdown;
        $values         =   [
            'perm'  =>  $permissionName,
        ];

        try {
            $permissionCheck    =   $lockdown->findPermissionByKey(
                $permissionKey
            );
        } catch (PermissionNotFound $e) {
        }

        if (isset($permissionCheck)) {
            $this->error('Permission [%(perm)s] already exists', $values);
            return;
        }
        
        $result         =   $lockdown->createPermission(
            $permissionName,
            $permissionKey,
            $description
        );

        if ($result) {
            $this->info("The permission [%(perm)s] has been created!", $values);
            return;
        }

        $this->error("The permission [%(perm)s] couldn't be created", $values);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['permission', InputArgument::REQUIRED, 'Name of the permission'],
            ['description', InputArgument::OPTIONAL, 'Description'],
        ];
    }
}
