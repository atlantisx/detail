<?php namespace Atlantis\Context\Conditions;

use Atlantis\Context\ConditionInterface;


class RouteIsCondition implements ConditionInterface {

    public function check(){
        $parameters = func_get_args();

        if(empty($parameters)) return false;

        return ( $parameters[0] == \Route::currentRouteName() );
    }
}