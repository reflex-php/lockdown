<?php namespace Reflex\Lockdown\Commands;

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
        $headers    =   ['ID', 'Name', 'Key', 'Description', 'Created At', 'Updated At'];
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
