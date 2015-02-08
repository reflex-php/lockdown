<?php namespace Reflex\Lockdown\Commands;

class ShowPermissions extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name         =   'lockdown:show-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description  =   'Show all permissions';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $headers    =   ['ID', 'Name', 'Key', 'Description', 'Created At', 'Updated At'];
        $permissions=   $this->lockdown->findAllPermissions();

        if (0 === count($permissions)) {
            $this->comment('No permissions could be found');
            return;
        }

        $permissions    =   array_map(
            function ($permission) {
                return $permission->toArray();
            },
            $permissions
        );

        $this->table($headers, $permissions);
    }
}
