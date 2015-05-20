<?php
namespace Reflex\Lockdown;

use Reflex\Lockdown\Roles\Eloquent\Provider as RoleProvider;
use Reflex\Lockdown\Users\Eloquent\Provider as UserProvider;
use Reflex\Lockdown\Permissions\Eloquent\Provider as PermissionProvider;
use Reflex\Lockdown\Commands;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Application;

class LockdownServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer    =    false;

    /**
     * Model configuration path
     * @var string
     */
    protected $modelsConfigPath    =   '/config/lockdown.php';

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerLockdownMigrations();
        $this->registerAuthTakeover();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerLockdownConfiguration();
        $this->registerLockdownFacade();
        $this->registerUserProvider();
        $this->registerRoleProvider();
        $this->registerPermissionProvider();
        $this->registerLockdown();
        $this->registerLockdownGuard();
        $this->registerCommands();
    }

    protected function registerLockdownMigrations()
    {
        if (method_exists($this, 'publishes')) {
            $this->publishes(
                [
                __DIR__ . '/migrations/'=>    base_path('/database/migrations'),
                ],
                'migrations'
            );
        }
    }

    /**
     * Register Lockdown configuration
     * @return void
     */
    protected function registerLockdownConfiguration()
    {
        $this->publishes(
            [
                __DIR__ . $this->modelsConfigPath    =>
                    config_path('lockdown.php'),
            ],
            'config'
        );

        $this->mergeConfigFrom(
            __DIR__ . $this->modelsConfigPath,
            'lockdown'
        );
    }

    /**
     * Register Lockdown
     * @return void
     */
    protected function registerLockdownGuard()
    {
        $this->app['lockdown.guard']    =    $this->app->share(
            function ($app) {
                $model          =   config('lockdown.user');
                $hasherInstance =   $app['hash'];
                $provider       =   new \Illuminate\Auth\EloquentUserProvider(
                    $hasherInstance,
                    $model
                );
                $lockdown       =    $app['lockdown'];
                $sessionStore   =    $app['session.store'];
                $requestInstance=    $app['request'];

                $lockdownGuard  =    new LockdownGuard(
                    $lockdown,
                    $provider,
                    $sessionStore,
                    $requestInstance
                );

                return $lockdownGuard;
            }
        );
    }

    /**
     * Register Artisan commands
     * @return void
     */
    protected function registerCommands()
    {
        $namespace    =    __NAMESPACE__ . '\\Commands\\';
        $keyPrefix    =    'lockdown.commands.';
        $commands    =    [
            $keyPrefix . 'assign-perm-user'  =>    'AssignPermissionToUser',
            $keyPrefix . 'assign-role-perm'  =>    'AssignPermissionToRole',
            $keyPrefix . 'assign-role-user'  =>    'AssignRoleToUser',
            $keyPrefix . 'create-perm'       =>    'CreatePermission',
            $keyPrefix . 'create-role'       =>    'CreateRole',
            $keyPrefix . 'delete-permission' =>    'DeletePermission',
            $keyPrefix . 'delete-role'       =>    'DeleteRole',
            $keyPrefix . 'remove-perm-user'  =>    'RemovePermissionFromUser',
            $keyPrefix . 'remove-role-perm'  =>    'RemovePermissionFromRole',
            $keyPrefix . 'remove-role-user'  =>    'RemoveRoleFromUser',
            $keyPrefix . 'show-permissions'  =>    'ShowPermissions',
            $keyPrefix . 'show-roles'        =>    'ShowRoles',
        ];

        foreach ($commands as $key => $class) {
            $this->app->bind(
                $key,
                function ($app) use ($class, $namespace) {
                    $classNamespaceFull    =    $namespace . $class;
                    return new $classNamespaceFull($app['lockdown']);
                }
            );
        }

        $this->commands(array_keys($commands));
    }

    /**
     * Register Auth extension
     * @return void
     */
    protected function registerAuthTakeover()
    {
        $this->app['auth']->extend(
            'lockdown',
            function ($app) {
                return $app['lockdown.guard'];
            }
        );
    }

    /**
     * Register user provider
     * @return void
     */
    protected function registerUserProvider()
    {
        $this->app['lockdown.user']    =    $this->app->share(
            function ($app) {
                $userModel    =    config('lockdown.user');
                return new UserProvider($userModel);
            }
        );
    }

    /**
     * Register role provider
     * @return void
     */
    protected function registerRoleProvider()
    {
        $this->app['lockdown.role']    =    $this->app->share(
            function ($app) {
                $roleModel    =    config('lockdown.role');
                return new RoleProvider($roleModel);
            }
        );
    }

    /**
     * Register permission provider
     * @return void
     */
    protected function registerPermissionProvider()
    {
        $this->app['lockdown.permission']    =    $this->app->share(
            function ($app) {
                $permissionModel    =    config('lockdown.permission');
                return new PermissionProvider($permissionModel);
            }
        );
    }

    /**
     * Register Lockdown
     * @return void
     */
    protected function registerLockdown()
    {
        $this->app['lockdown']    =    $this->app->share(
            function ($app) {
                $userProvider       =    $app['lockdown.user'];
                $roleProvider       =    $app['lockdown.role'];
                $permissionProvider =    $app['lockdown.permission'];
                return new Lockdown(
                    $userProvider,
                    $roleProvider,
                    $permissionProvider
                );
            }
        );
        $this->app->alias('lockdown', 'Reflex\Lockdown\Lockdown');
    }

    /**
     * Register the Lockdown Facade
     * @return void
     */
    protected function registerLockdownFacade()
    {
        $this->app->booting(
            function () {
                $loader    =    AliasLoader::getInstance();
                $loader->alias('Lockdown', __NAMESPACE__ . '\LockdownFacade');
            }
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'lockdown',
            'lockdown.commands.assign-perm-user',
            'lockdown.commands.assign-role-perm',
            'lockdown.commands.assign-role-user',
            'lockdown.commands.create-perm',
            'lockdown.commands.create-role',
            'lockdown.commands.delete-permission',
            'lockdown.commands.delete-role',
            'lockdown.commands.remove-perm-user',
            'lockdown.commands.remove-role-perm',
            'lockdown.commands.remove-role-user',
            'lockdown.commands.show-permissions',
            'lockdown.commands.show-roles',
            'lockdown.guard',
            'lockdown.permission',
            'lockdown.role',
            'lockdown.user',
        ];
    }
}
