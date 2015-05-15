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

use Reflex\Lockdown\Exceptions\RoleNotFound;
use Illuminate\Database\QueryException;

/**
 * Create a role
 * @category Package
 * @package  Reflex
 * @author   Mike Shellard <contact@mikeshellard.me>
 * @license  http://mikeshellard.me/reflex/license MIT
 * @link     http://mikeshellard.me/reflex/lockdown
 */
class CreateRole extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name         =   'lockdown:create-role';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description  =   'Create a new role';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $roleName   =   $this->argument('role');
        $description=   $this->argument('description');
        $roleKey    =   snake_case($roleName);
        $lockdown   =   $this->lockdown;
        $values     =   [
            'role'  =>  $roleName,
        ];

        try {
            $roleCheck  =   $lockdown->findRoleById($roleKey);
        } catch (RoleNotFound $e) {
        }

        if (isset($roleCheck) && $roleCheck) {
            $this->error('Role [%(role)s] already exists', $values);
            return;
        }

        try {
            $result =   $lockdown->createRole(
                $roleName,
                $roleKey,
                $description
            );
        } catch (QueryException $e) {
            $this->error(
                "The role [%(role)s] couldn't be created due to a " .
                "'QueryException', please check your error log.",
                $values
            );
            return;
        }

        if ($result) {
            $this->info(
                "The role [%(role)s] (%(key)s) has been created!",
                $values
            );
            return;
        }

        $this->error("The role [%(role)s] couldn't be created", $values);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['role', InputArgument::REQUIRED, 'Name of the role'],
            ['description', InputArgument::OPTIONAL, 'Description'],
        ];
    }
}
