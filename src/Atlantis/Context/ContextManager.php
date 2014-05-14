<?php namespace Atlantis\Context;

use Closure;
use Illuminate\Support\Collection;
use Atlantis\Context\Enums;
use Atlantis\Context\Conditions;


class ContextManager {

    /**
     *
     *
     * @var array
     */
    protected $contexts;


    /**
     * Conditions provider array
     *
     * @var array
     */
    protected $conditions = [];

    /**
     *
     *
     * @var array
     */
    protected $condition_extensions = [];

    /**
     * Reactions provider array
     *
     * @var array
     */
    protected $reactions = [];

    /**
     *
     *
     * @var array
     */
    protected $reaction_extensions = [];


    /**
     *
     * @param $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->contexts = new Collection([]);
    }


    /**
     * Get condition provider
     *
     * @param $name
     * @return mixed
     */
    public function condition($name)
    {
        #i: Check for extend / override
        if( isset($this->condition_extensions[$name]) ){
            return $this->condition_extensions[$name];

        #i: If condition provider not exist load
        }elseif ( !isset($this->conditions[$name]) ){
            #i: Proper case name
            $condition_provider_name = studly_case($name);

            #i: Get provider full namespace
            $condition_provider_path = "Atlantis\\Context\\Conditions\\{$condition_provider_name}Condition";

            #i: Get provider instance
            $this->conditions[$name] = new $condition_provider_path;
        }

        #i: Return provider
        return $this->conditions[$name];
    }


    /**
     * Get reaction provider
     *
     * @param $name
     * @return mixed
     */
    public function reaction($name)
    {
        #i: Check for extend / override
        if( isset($this->reaction_extensions[$name]) ){
            return $this->reaction_extensions[$name];

            #i: If condition provider not exist load
        }elseif ( !isset($this->reactions[$name]) ){
            #i: Proper case name
            $reaction_provider_name = studly_case($name);

            #i: Get provider full namespace
            $reaction_provider_path = "Atlantis\\Context\\Reactions\\{$reaction_provider_name}Reaction";

            #i: Get provider instance
            $this->reactions[$name] = new $reaction_provider_path;
        }

        #i: Return provider
        return $this->reactions[$name];
    }


    /**
     *
     *
     * @return mixed
     */
    public function reactionInspect($name,$arguments){
        $run = $this->reaction($name);

        if( get_class($run) == 'Closure' ){
            return call_user_func_array($run,$arguments);
        }else{
            return $run->run($arguments);
        }

        return null;
    }


    /**
     *
     *
     * @return mixed
     */
    public function conditions()
    {
        return $this->conditions;
    }


    /**
     *
     *
     * @return mixed
     */
    public function reactions()
    {
        return $this->reactions;
    }


    /**
     *
     *
     * @return void
     */
    public function extendCondition($name, Closure $callback)
    {
        $this->conditions_extension[$name] = $callback;
    }


    /**
     *
     *
     * @return void
     */
    public function extendReaction($name, Closure $callback)
    {
        $this->reaction_extensions[$name] = $callback;
    }


    /**
     *
     *
     * @return void
     */
    public function set($context){
        $this->contexts->put($context->id,$context);
    }


    /**
     *
     *
     * @return mixed
     */
    public function __call($method, $parameters){
        return call_user_func_array([$this->contexts,$method],$parameters);
    }
}
