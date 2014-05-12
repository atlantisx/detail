<?php namespace Atlantis\Detail\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;


class Detail extends Eloquent {
    protected $table = 'applications';
    protected $primaryKey = 'name';
    protected $guarded = array('id');
    public $appends = array('cards','page_title');
    public $timestamps = false;


    public function __construct(){
        //$this->filters();
    }


    public function records(){
        return $this->hasMany('\Atlantis\Detail\Model\Record');
    }


    public function contexts(){
        return $this->hasMany('\Atlantis\Context\Model\Context');
    }


    public function workflows($path){
        $patterns = [
            'control'   => [
                'count'
            ],
            'resource'  => [
                'status' => 'Atlantis\Workflow\Resources\Status'
            ],
            'data'      => [
                'status'    => 'records.status'
            ],
        ];

        $pattern = array_get($patterns,$path);

        if( isset($pattern) ){
            return \App::make($pattern);
        }

        return null;
    }


    public function filters(){
        #i: Get filters
        $filters = ['context'];

        #i: Execute filters
        foreach( $filters as $filter ){
            $this->{$filter.'Filter'}();
        }
    }


    public function contextFilter(){
        foreach( $this->contexts()->all() as $context ){

        }
    }


    public function getConfigs($name=null){
        if( $name ){
            if( isset($this->config->{$name}) ) return $this->config->{$name};

        }else{
            return $this->config;
        }
    }


    public function getConfigAttribute($value){
        if( empty($value) ) $value = '{}';
        return json_decode($value);
    }


    public function getCardsAttribute($value){
        if( empty($value) ) $value = '{}';
        return json_decode($value);
    }


    public function getPageTitleAttribute(){
        $route_name = Route::currentRouteName();
        $menus = $this->getMenusAttribute();

        return ( isset($menus['items'][$route_name]) ? $menus['items'][$route_name] : $menus['title'] );
    }


    public function getMenusAttribute(){
        return Config::get('admin::admin.sidebar.applications.'.$this->name);
    }
}
