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

/**
 * Show roles
 * @category Package
 * @package  Reflex
 * @author   Mike Shellard <contact@mikeshellard.me>
 * @license  http://mikeshellard.me/reflex/license MIT
 * @link     http://mikeshellard.me/reflex/lockdown
 */
class ShowRoles extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name         =   'lockdown:show-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description  =   'Show all roles';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $headers    =   [
            'ID',
            'Name',
            'Key',
            'Description',
            'Created At',
            'Updated At'
        ];
        $roles      =   $this->lockdown->findAllRoles();

        if (0 === count($roles)) {
            $this->comment('No roles could be found');
            return;
        }

        $roles  =   array_map(
            function ($role) {
                return $role->toArray();
            },
            $roles
        );

        $this->table($headers, $roles);
    }
}
