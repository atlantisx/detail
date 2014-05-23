<?php namespace Atlantis\Detail\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Rhumsaa\Uuid\Uuid;


class Record extends Eloquent {
    protected $table = 'records';
    protected $primaryKey = 'uuid';
    protected $guarded = array('uuid','created_when','updated_when');
    protected $appends = array('created_when','updated_when');
    public $incrementing = false;


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Uuid::uuid4();
        });

        static::deleting(function ($detail){
            $detail->documents()->delete();
        });
    }


    public function detail(){
        return $this->belongsTo('Detail','application_id','id');
    }


    public function documents(){
        return $this->hasMany('Document','detail_uuid');
    }


    public function getMetaAttribute($value){
        if( empty($value) ) $value = '{}';
        return json_decode($value);
    }

    public function setMetaAttribute($value){
        if( is_array($value) || is_object($value) ){
            $this->attributes['meta'] = json_encode($value);
        }else{
            $this->attributes['meta'] = $value;
        }
    }


    public function getCreatedWhenAttribute(){
        return \Carbon\Carbon::createFromTimeStamp(strtotime($this->created_at))->diffForHumans();
    }


    public function getUpdatedWhenAttribute(){
        return \Carbon\Carbon::createFromTimeStamp(strtotime($this->updated_at))->diffForHumans();
    }


    public function scopeFiltering($query,$columns=array()){
        foreach($columns as $column => $value){
            $array_column =  explode('.',$column);
            $field = array_pop($array_column);
            $field_last = last($array_column);

            if( count($array_column) > 0 ){
                array_reduce($array_column, function(&$collector,$item) use($field_last, $field, $value){
                    $collector->whereHas($item, function($query) use(&$collector,$item,$field_last, $field, $value){
                        if($item == $field_last){
                            $query->where($field,'LIKE',$value.'%');
                        }
                        $collector = $query;
                    });

                    return $collector;
                },$query);

            }else{
                #i: Filtering normal columns
                $query->where($field,'LIKE',$value.'%');
            }
        }
    }


    protected function columnFilter($query, $array_column, $value){
        $relation = array_pop($array_column);
        $field = last($array_column);

        $query->whereHas($relation, function($query) use($array_column, $field, $relation, $value){
            if($relation == $field){
                $query->where($field,'LIKE',$value.'%');
            }else{
                $this->columnFilter($query,$array_column,$value);
            }
        });
    }
}