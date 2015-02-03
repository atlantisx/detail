<?php namespace Atlantis\Detail\Model;

use Atlantis\Core\Model\BaseModel;

class Detail extends BaseModel {
    protected $table = 'details';
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

        if( !empty($pattern) ){
            return app($pattern);
        }

        return null;
    }


    public function getConfigAttribute($value){
        if( empty($value) ) $value = '{}';
        return json_decode($value);
    }

}
