<?php namespace Laravella\Crud;

use Illuminate\Support\ServiceProvider;
use Laravella\Crud;

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

        // Register 'underlyingclass' instance container to our UnderlyingClass object
        $this->app['options'] = $this->app->share(function($app)
                {
                    return new Options;
                });

        $this->app->booting(function()
                {
                    $loader = \Illuminate\Foundation\AliasLoader::getInstance();
                    $loader->alias('DbGopher', 'Laravella\Crud\Facades\DbGopher');
                    $loader->alias('Options', 'Laravella\Crud\Facades\Options');
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

        $this->app['command.crud.update'] = $this->app->share(function($app){return new CrudUpdateCommand();});
        $this->app['command.crud.install'] = $this->app->share(function($app){return new CrudInstallCommand();});
        
        $this->commands(
                'command.crud.update', 'command.crud.install'
        );
    }


}