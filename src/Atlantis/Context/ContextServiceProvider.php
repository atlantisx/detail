<?php namespace Atlantis\Context;

use Illuminate\Support\ServiceProvider;


class ContextServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;


	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bindShared('context', function($app){
            return new ContextManager($app);
        });

        $this->app->bindShared('context.condition', function($app){
            $app['context']->conditions();
        });

        $this->app->bindShared('context.reactions', function($app){
            $app['context']->reactions();
        });
	}


	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('context','context.condition','context.reactions');
	}

}
