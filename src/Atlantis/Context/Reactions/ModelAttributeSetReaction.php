<?php

namespace Atlantis\Context\Reactions;

use Atlantis\Context\ReactionInterface;


class ModelAttributeSetReaction implements ReactionInterface {

    public function run(){
        #i: Get arguments
        list($model,$attribute,$original,$parameters) = func_get_args();

        #i: Reaction task
        if( $parameters->attribute != $attribute ) return null;

        // Types supported : static, model
        //
        // Static required parameter :-
        // - value
        //
        // Model Parameters :-
        // - reference
        // - reference_key
        // - reference_value
        // - reference_filter

        $type = !empty($parameters->type) ? $parameters->type : 'static';

        if( $type == 'static' )
            return $this->getStaticValue($parameters);

        elseif( $type == 'model' )
            return $this->getModelValue($model,$original,$parameters);
    }


    private function getStaticValue($parameters){
        return $parameters->value;
    }


    private function getModelValue($model,$original,$parameters){
        $references = app($parameters->reference);

        if( $references ){
            if( !empty($parameters->reference_filter) )
                $references->whereRaw($parameters->reference_filter);

            $references = $references->where($parameters->reference_key,$original);
            $founded = $references->first();

            return $founded->{$parameters->reference_value};
        }

        return '';
    }
}