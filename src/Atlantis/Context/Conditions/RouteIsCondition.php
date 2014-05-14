<?php namespace Atlantis\Context\Conditions;

use Atlantis\Context\ConditionInterface;


class RouteIsCondition implements ConditionInterface {

    public function check(){
        #i: Get arguments
        list($route,$parameters) = func_get_args();

        #i: Check arguments
        if(empty($parameters)) return false;

        #i: Condition task
        return ( $parameters->name == $route->getName() );
    }
}