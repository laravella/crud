<?php

namespace Laravella\Crud;

use Illuminate\Support\ServiceProvider;

class CrudServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('laravella/crud');

        include __DIR__ . '/../../routes.php';

        $this->registerCommands();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Register 'underlyingclass' instance container to our UnderlyingClass object
        $this->app['dbgopher'] = $this->app->share(function($app)
                {
                    return new DbGopher;
                });

        $this->app->booting(function()
                {
                    $loader = \Illuminate\Foundation\AliasLoader::getInstance();
                    $loader->alias('DbGopher', 'Laravella\Crud\Facades\DbGopher');
                });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    /** register the custom commands * */
    public function registerCommands()
    {
//            Artisan::add(new InstallCommand);
//            Artisan::add(new UpdateCommand);

        $commands = array('CrudBackup', 'CrudInstall', 'CrudRestore');

        foreach ($commands as $command)
        {
            $this->{'register' . $command . 'Command'}();
        }

        $this->commands(
                'command.crud.backup', 'command.crud.restore', 'command.crud.install'
        );
    }

    public function registerCrudBackupCommand()
    {
        $this->app['command.crud.backup'] = $this->app->share(function($app)
                {
                    return new CrudBackupCommand();
                });
    }

    public function registerCrudInstallCommand()
    {
        $this->app['command.crud.install'] = $this->app->share(function($app)
                {
                    return new CrudInstallCommand();
                });
    }

    public function registerCrudRestoreCommand()
    {
        $this->app['command.crud.restore'] = $this->app->share(function($app)
                {
                    return new CrudRestoreCommand();
                });
    }

}