<?php namespace Atlantis\Context\Reactions;

use Atlantis\Context\ReactionInterface;


class AttributeSetReaction implements ReactionInterface {

    public function run(){
        #i: Get arguments
        list($model,$attribute,$parameters) = func_get_args();

        #i: Reaction task
        if( $parameters->attribute == $attribute) return $parameters->value;
    }

}