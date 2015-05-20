<?php /**
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

/**
 * Delete a role
 * @category Package
 * @package  Reflex
 * @author   Mike Shellard <contact@mikeshellard.me>
 * @license  http://mikeshellard.me/reflex/license MIT
 * @link     http://mikeshellard.me/reflex/lockdown
 */
class DeleteRole extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name         =   'lockdown:delete-role';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description  =   'Delete a role';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $roleKey    =   snake_case($this->argument('role'));
        $lockdown   =   $this->lockdown;
        $values     =   [
            'role'  =>  $roleKey,
        ];

        try {
            $role   =   $lockdown->findRoleByKey($roleKey);
        } catch (RoleNotFound $e) {
            $this->error('Role [%(role)s] doesn\'t exist', $values);
            return;
        }

        $result     =   $lockdown->deleteRole($roleKey);

        if (false !== $result) {
            $this->info("The role [%(role)s] has been deleted!", $values);
            return;
        }

        $this->error("The role [%(role)s] couldn't be deleted", $values);
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
        ];
    }
}
