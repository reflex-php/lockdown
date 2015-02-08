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
	protected $defer	=	false;

	/**
	 * Cache configuration path
	 * @var string
	 */
	protected $cacheConfigPath	=   '/config/cache.php';

	/**
	 * Model configuration path
	 * @var string
	 */
	protected $modelsConfigPath	=   '/config/models.php';

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
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
		$this->registerLockdownCacheLayer();
		$this->registerUserProvider();
		$this->registerRoleProvider();
		$this->registerPermissionProvider();
		$this->registerLockdown();
		$this->registerLockdownGuard();
		$this->registerCommands();
	}

	/**
	 * Register Lockdown configuration
	 * @return void 
	 */
	protected function registerLockdownConfiguration()
	{
		// Are we running an old or legacy version of Laravel?
		if ($this->isLegacyLaravel() || $this->isOldLaravel()) {
			$this->package('reflex/lockdown', 'lockdown', __DIR__ . '/app');
		} else {
			$this->publishes(
	        	[
	        		__DIR__ . $this->cacheConfigPath	=>	
	        			config_path('lockdown-cache.php'),
	        		__DIR__ . $this->modelsConfigPath	=>	
	        			config_path('lockdown-models.php'),
	        	],
	        	'config'
	        );

	        $this->mergeConfigFrom(
	        	__DIR__ . $this->cacheConfigPath, 
	        	'lockdown.cache'
	        );
	        $this->mergeConfigFrom(
	        	__DIR__ . $this->modelsConfigPath, 
	        	'lockdown.models'
	        );
		}
	}

	/**
	 * Register Lockdown
	 * @return void 
	 */
	protected function registerLockdownGuard()
	{
		$this->app['lockdown.guard']	=	$this->app->share(
			function ($app) {
				$model      	=   __NAMESPACE__ . '\Users\Eloquent\User';
				$hasherInstance	=	$app['hash'];
			    $provider   	=   new \Illuminate\Auth\EloquentUserProvider(
			        $hasherInstance,
			        $model
			    );
			    $lockdown		=	$app['lockdown'];
			    $sessionStore	=	$app['session.store'];
			    $requestInstance=	$app['request'];

				$lockdownGuard	=	new LockdownGuard(
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
		$namespace	=	__NAMESPACE__ . '\\Commands\\';
		$keyPrefix	=	'lockdown.commands.';
		$commands	=	[
			$keyPrefix . 'show-roles'		=>	'ShowRoles',
			$keyPrefix . 'show-permissions'	=>	'ShowPermissions',

			$keyPrefix . 'create-role'		=>	'CreateRole',
			$keyPrefix . 'create-perm'		=>	'CreatePermission',

			$keyPrefix . 'delete-permission'=>	'DeletePermission',
			$keyPrefix . 'delete-role'		=>	'DeleteRole',

			$keyPrefix . 'assign-role-perm'	=>	'AssignPermissionToRole',
			$keyPrefix . 'assign-role-user'	=>	'AssignRoleToUser',
			$keyPrefix . 'assign-perm-user'	=>	'AssignPermissionToUser',

			$keyPrefix . 'remove-perm-user'	=>	'RemovePermissionFromUser',
			$keyPrefix . 'remove-role-user'	=>	'RemoveRoleFromUser',
			$keyPrefix . 'remove-role-perm'	=>	'RemovePermissionFromRole',
		];

		foreach ($commands as $key => $class) {
			$this->app->bind(
				$key,
				function ($app) use ($class, $namespace) {
					$classNamespaceFull	=	$namespace . $class;
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
				$lockdownAuthGuardExtension	=	$app['lockdown.guard'];
				return $lockdownAuthGuardExtension;
			}
		);
	}

	/**
	 * Register lockdown cache layer
	 * @return void 
	 */
	protected function registerLockdownCacheLayer()
	{
		$this->app['lockdown.cache']	=	$this->app->share(
			function ($app) {
				$config			=	$app['config'];
				$lockdownConfig	=	$config->get('lockdown.cache');
				$cacheManager	=	$app['cache'];
				$isEnabled		=	$lockdownConfig['enabled'];
				$expiry			=	$lockdownConfig['expire'];
				$cacheId		=	$lockdownConfig['id'];

				return new LockdownCacheLayer(
					$cacheManager,
					$expiry,
					$expiry,
					$cacheId
				);
			}
		);
	}

	/**
	 * Register user provider
	 * @return void 
	 */
	protected function registerUserProvider()
	{
		$this->app['lockdown.user']	=	$this->app->share(
			function ($app) {
				$userModel	=	$app['config']['lockdown.models.user'];
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
		$this->app['lockdown.role']	=	$this->app->share(
			function ($app) {
				$roleModel	=	$app['config']['lockdown.models.role'];
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
		$this->app['lockdown.permission']	=	$this->app->share(
			function ($app) {
				$permissionModel	=	$app['config']['lockdown.models.permission'];
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
		$this->app['lockdown']	=	$this->app->share(
			function ($app) {
				$lockdownCache		=	$app['lockdown.cache'];
				$userProvider		=	$app['lockdown.user'];
				$roleProvider		=	$app['lockdown.role'];
				$permissionProvider	=	$app['lockdown.permission'];
				return new Lockdown(
					$lockdownCache,
					$userProvider,
					$roleProvider,
					$permissionProvider
				);
			}
		);
	}

	/**
	 * Register the Lockdown Facade
	 * @return void 
	 */
	protected function registerLockdownFacade()
	{
		$this->app->booting(
			function () {
				$loader	=	AliasLoader::getInstance();
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
			'lockdown.user',
			'lockdown.role',
			'lockdown.permission',
			'lockdown.guard',
			'lockdown.commands.show-roles',
			'lockdown.commands.show-permissions',
			'lockdown.commands.create-role',
			'lockdown.commands.create-perm',
			'lockdown.commands.delete-role',
			'lockdown.commands.delete-permission',
			'lockdown.commands.assign-role-perm',
			'lockdown.commands.assign-role-user',
			'lockdown.commands.assign-perm-user',
			'lockdown.commands.remove-perm-user',
			'lockdown.commands.remove-role-user',
			'lockdown.commands.remove-role-perm',
		];
	}

	/**
	 * Is the application running a legacy version of Laravel?
	 * @return boolean 
	 */
	public function isLegacyLaravel()
	{
		return Str::startsWith(Application::VERSION, array('4.1.', '4.2.'));
	}

	/**
	 * Is the application running an old version of Laravel?
	 * @return boolean 
	 */
	public function isOldLaravel()
	{
		return Str::startsWith(Application::VERSION, '4.0.');
	}
}
