<?php namespace Atlantis\Detail\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Rhumsaa\Uuid\Uuid;


class Record extends Eloquent {
    protected $table = 'details';
    protected $primaryKey = 'uuid';
    protected $guarded = array('uuid','message','user','guardian','guarantor','url_update','application','status_title','status_label','url_update','created_when','updated_when');
    protected $appends = array('status_title','status_label','url_update','created_when','updated_when');
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Uuid::uuid1();
        });

        static::deleting(function ($detail){
            $detail->documents()->delete();
        });
    }


    public function detail(){
        return $this->belongsTo('Atlantis\Detail\Model\Detail');
    }


    public function user(){
        return $this->belongsTo('User');
    }


    public function documents(){
        return $this->hasMany('Document','detail_uuid');
    }


    public function guardian(){
        return $this->hasOne('People','id','guardian_id');
    }


    public function guarantor(){
        return $this->hasOne('People','id','guarantor_id');
    }


    public function getStatusTitleAttribute(){
        return $this->status_title();
    }


    public function getStatusLabelAttribute(){
        return $this->status_label();
    }


    public function getCreatedWhenAttribute(){
        return \Carbon\Carbon::createFromTimeStamp(strtotime($this->created_at))->diffForHumans();
    }


    public function getUpdatedWhenAttribute(){
        return \Carbon\Carbon::createFromTimeStamp(strtotime($this->updated_at))->diffForHumans();
    }


    public function getUrlUpdateAttribute(){
        #i: Check for application name from detail
        $application_name = ($this->application ? $this->application->name : 'default');

        #i: Return the update url
        return action('\Modules\Advance\ApplicationController@getUpdate',array($application_name,$this->uuid));
    }


    public function getMetaAttribute($value){
        if( empty($value) ) $value = '{}';
        return json_decode($value);
    }
    public function setMetaAttribute($value){
        $this->attributes['meta'] = json_encode($value);
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

    protected function status_title($status=null){
        if( !isset($status) ) $status = $this->status;

        //[i] Get general status
        $status_title = trans( 'advance.status.general.'.$status);

        //[i] Declared status group for student
        $status_student = array(
            1 => array(1,2,3,4,5,6),
            2 => array(7),
            3 => array(8)
        );

        //[i] Get diff status if role is student
        $user = \Sentry::getUser();
        if( $user ){
            $user_realm = \App::make('atlantis.realm')->byUserId($user->id);

            if($user_realm->name == 'student'){
                foreach( $status_student as $key => $value ){
                    if( in_array($status,$value) ){
                        $status_title = trans( 'advance.status.student.'.$key);
                        break;
                    }
                }
            }
        }

        return $status_title;
    }


    public static function status_count($statuses){
        $status_count = array();

        foreach($statuses as $status){
            $status_count[$status] = Record::where('status','=',$status)->count();
        }

        return $status_count;
    }


    protected function status_label($status=null){
        if( !isset($status) ) $status = $this->status;
        return $status_title = trans( 'advance.status_label.'.$status);;
    }

    protected function progress_percent(){
        $incomplete = array();
        $required = array(
            $this->institution_name,             //institusi
            $this->institution_code,             //kod_institusi
            $this->institution_state,            //negeri_institusi
            $this->application_location,         //tempat_memohon
            $this->application_coursed,          //kursus
            $this->application_date,             //tkh_permohonan
            $this->application_existing,         //pernah_memohon
            $this->course_level,                 //peringkat_pengajian
            $this->course_code,                  //kod_kursus
            $this->course_start,                 //tkh_mula_kursus
            $this->course_end,                   //tkh_tamat_kursus
            $this->guardian_id,
            $this->guardian_salary_gross,       //pendapatan_kasar_penjaga
            $this->guardian_family_no,          //bil_tanggungan_penjaga
            $this->guardian_employment,         //pekerjaan_penjaga
            $this->guarantor_id,
            $this->guarantor_status,            //status_penjamin
            $this->guarantor_salary_gross,      //pendapatan_kasar_penjamin
            $this->amount_total                 //jum_dipohon
        );

        foreach( $required as $field){
            if( empty($field) ) array_push($incomplete, $field);
        }

        return round( ( count($incomplete) / count($required) ) * 100 );
    }
}