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
            return $app['context']->conditions();
        });

        $this->app->bindShared('context.reactions', function($app){
            return $app['context']->reactions();
        });

        $this->app->bind('context.model', function($app){
           return new Model\Context;
        });
	}


    /**
     * Boot service provider
     *
     */
    public function boot(){
        $this->bootConditionRouter();
    }


    /**
     * Condition Boot : Router Category
     *
     */
    function bootConditionRouter(){
        $this->app['router']->matched(function($route,$request){
            #i: Get all route condition context
            $contexts = $this->app['context.model']->whereConditionType('route')->get();    // Will changed to router

            #i: Iterate to execute every context provider
            foreach( $contexts as $context ){
                #i: Get context condition provider
                $provider = $this->app['context']->condition($context->condition_provider);

                #i: Check condition
                if( $provider->check($route,$context->condition_parameters) ){
                    $this->app['context']->set($context);
                };
            }
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
