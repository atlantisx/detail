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

    public $timestamps = false;

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
        if( gettype($value) == 'object' || gettype($value) == 'array' ){
            $this->attributes['condition_parameters'] = json_encode($value);
        }else{
            $this->attributes['condition_parameters'] = $value;
        }
    }


    public function getReactionParametersAttribute($value){
        if( json_decode($value) ) {
            return json_decode($value);
        }else{
            return $value;
        }
    }

    public function setReactionParametersAttribute($value){
        if( gettype($value) == 'object' || gettype($value) == 'array' ){
            $this->attributes['reaction_parameters'] = json_encode($value);
        }else{
            $this->attributes['reaction_parameters'] = $value;
        }
    }

}