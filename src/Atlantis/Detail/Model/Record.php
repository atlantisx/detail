<?php

namespace Atlantis\Detail\Model;

use Rhumsaa\Uuid\Uuid;
use Carbon\Carbon;
use Atlantis\Core\Model\BaseModel;


class Record extends BaseModel {
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


    public function getCreatedWhenAttribute(){
        return Carbon::createFromTimeStamp(strtotime($this->created_at))->diffForHumans();
    }


    public function getUpdatedWhenAttribute(){
        return Carbon::createFromTimeStamp(strtotime($this->updated_at))->diffForHumans();
    }


    public function scopeFiltering($query,$columns=array()){
        foreach($columns as $column => $value){
            $relations =  explode('.',$column);
            $field = array_pop($relations);
            $relation = head($relations);

            if( count($relations) > 0 ){
                $query->whereHas($relation, function($q) use($relations,$field,$value){
                    $relation = array_pop($relations);
                    if( count($relations) == 0 ){
                        $q->where($field,'LIKE',$value.'%');

                    }else{
                        $q->whereHas($relation, function($q) use($relations,$field,$value){
                            $relation = array_pop($relations);
                            if( count($relations) == 0 ){
                                $q->where($field,'LIKE',$value.'%');

                            }else{
                                $q->whereHas($relation, function($q) use($relations,$field,$value){
                                    $relation = array_pop($relations);
                                    if( count($relations) == 0 ){
                                        $q->where($field,'LIKE',$value.'%');
                                    }
                                });
                            }
                        });
                    }
                });

            }else{
                /** Filtering normal columns */
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