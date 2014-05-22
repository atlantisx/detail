<?php namespace Atlantis\Context\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;


class Context extends Eloquent{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'contexts';

    protected $guarded = ['id'];

    public function detail(){
        return $this->belongsTo('\Atlantis\Detail\Model\Detail');
    }


    public function getConditionParametersAttribute($value){
        if( json_decode($value) ) {
            return json_decode($value);
        }else{
            return $value;
        }
    }


    public function setConditionParametersAttribute($value){
        $this->attributes['condition_parameters'] = json_encode($value);
    }


    public function getReactionParametersAttribute($value){
        if( json_decode($value) ) {
            return json_decode($value);
        }else{
            return $value;
        }
    }

    public function setReactionParametersAttribute($value){
        $this->attributes['reactions_parameters'] = json_encode($value);
    }

}