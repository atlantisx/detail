<?php namespace Atlantis\Detail\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Underscore\Types\Arrays;


class Detail extends Eloquent {
    protected $table = 'applications';
    protected $primaryKey = 'name';
    protected $guarded = array('id');
    public $appends = array('cards');
    public $timestamps = false;


    public function records(){
        return $this->hasMany('Record','application_id','id');
    }


    public function contexts(){
        return $this->hasMany('Atlantis\Context\Model\Context','detail_id','id');
    }


    public function workflows($path){
        $patterns = [
            'control'   => [
            ],
            'resource'  => [
                'status' => 'Atlantis\Workflow\Resources\Status'
            ],
            'data'      => [
            ],
        ];

        $pattern = array_get($patterns,$path);

        if( isset($pattern) ){
            return \App::make($pattern);
        }

        return null;
    }


    public function getConfigAttribute($value){
        if( empty($value) ) $value = '{}';
        return json_decode($value);
    }


    public function getAttribute($key){
        $value_original = null;

        #i: Parent original value
        if( parent::getAttribute($key) ){
            $value_original = parent::getAttribute($key);
        }

        #i: Context Reaction
        $context_value = $this->getContextReaction($key);
        if( $context_value ) $value_original = $context_value;

        return $value_original;
    }


    public function getContextReaction($key){
        #i: Get context
        $contexts = \App::make('context');

        #i: Filter Reaction Contexts
        $context = Arrays::find($contexts->all(), function($value){
            return $value->reaction_parameters->model == get_called_class();
        });

        #i: Inspect reaction context
        if($context){
            #i: Inspect context
            $value = $contexts->reactionInspect($context->reaction_provider,[$this,$key,$context->reaction_parameters]);

            #i: Override value
            if($value) return $value;
        }

        return null;
    }

    public function __isset($key){
        if( $this->getAttribute($key) ) return true;
    }
}
