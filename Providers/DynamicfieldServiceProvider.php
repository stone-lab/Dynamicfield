<?php namespace Modules\Dynamicfield\Providers;

use Illuminate\Support\ServiceProvider;
use Pingpong\Modules\Module;

class DynamicfieldServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot()
    {
        // override core setup to make StudlyName for module.
            /*$modules = $this->app['modules']->enabled();
            $curModule = $modules["Dynamicfield"];
            $moduleName = $curModule->getStudlyName();
             $this->app['view']->addNamespace(
                $moduleName,
                $curModule->getPath() . '/Resources/views'
            );
            $this->loadTranslationsFrom($curModule->getPath() . '/Resources/lang', $moduleName);*/
    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();
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

    private function registerBindings()
    {
        // add bindings
        $this->app->bind(
            'Modules\Dynamicfield\Repositories\FieldsRepository',
            function () {
                $repository = new \Modules\Dynamicfield\Repositories\Eloquent\EloquentFieldsRepository(new \Modules\Dynamicfield\Entities\Fields());
                if (! config('app.cache')) {
                    return $repository;
                }
                //return new \Modules\Dynamicfield\Repositories\Cache\CacheFieldsDecorator($repository);
            }
        );

        $this->app->bind(
            'Modules\Dynamicfield\Repositories\GroupRepository',
            function () {
                $repository = new \Modules\Dynamicfield\Repositories\Eloquent\EloquentGroupRepository(new \Modules\Dynamicfield\Entities\Group());

                  return $repository;
                //return new \Modules\Dynamicfield\Repositories\Cache\CacheFieldsDecorator($repository);
            }
        );

        $this->app->bind(
            'Modules\Dynamicfield\Repositories\GroupFieldRepository',
            function () {
                $repository = new \Modules\Dynamicfield\Repositories\Eloquent\EloquentGroupFieldRepository(new \Modules\Dynamicfield\Entities\Field());

                if (! config('app.cache')) {
                    return $repository;
                }

                //return new \Modules\Dynamicfield\Repositories\Cache\CacheFieldsDecorator($repository);
            }
        );

        if ($this->app->environment() == 'local') {
            $this->app->register('Barryvdh\Debugbar\ServiceProvider');
        }
    }
}
