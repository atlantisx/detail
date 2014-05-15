<?php namespace Atlantis\Workflow\Resources;

use ArrayAccess;
use Atlantis\View\Interfaces\Realm;


class Status implements ArrayAccess{
    private $container = [];
    protected $default = null;
    protected $realm;


    public function __construct(Realm $realm){
        $this->realm = $realm->current();

        $status = [
            'staff' => [
                0 => 'new',             //'Baru',
                1 => 'eligible',        //'Layak',
                2 => 'not_eligible',    //'Tidak Layak',
                3 => 'accept',          //'Terima',
                4 => 'support',         //'Sokong',
                5 => 'not_support',     //'Tidak Sokong',
                6 => 'consideration',   //'Dalam Pertimbangan',
                7 => 'pass',            //'Lulus',
                8 => 'not_pass',        //'Tidak Lulus',
                9 => 'payment',         //'Bayar',
                12 => 'complete',       //'Lengkap'
            ],
            'student' => [
                1 => 'in_process',      //'Sedang di Proses',
                2 => 'pass',            //'Lulus',
                3 => 'not_pass',        //'Tidak Lulus'
            ]
        ];

        $this->container = $status[$this->realm->name];
    }



    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->container[$offset]);
    }


    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }


    public function offsetGet($offset) {
        $label = array(
            0 => 'info',
            1 => 'warning',
            2 => 'red',
            3 => 'success',
            4 => 'success',
            5 => 'red',
            6 => 'warning',
            7 => 'success',
            8 => 'red',
            9 => 'success',
            12=> 'success'
        );

        $colour = array(
            0 => 'blue',
            1 => 'orange',
            2 => 'orange',
            3 => 'green',
            4 => 'green',
            5 => 'red',
            6 => 'orange',
            7 => 'green',
            8 => 'red',
            9 => 'green',
            12=> 'green'
        );

        #i: Get value
        if( isset($this->container[$offset]) ){
            return [
                'id'    => $offset,
                'name'  => $this->container[$offset],
                'title' => trans("advance::advance.status.{$this->realm->name}.".$this->container[$offset]),
                'label' => $label[$offset],
                'color' => $colour[$offset]
            ];
        }

        return null;
    }


    public function all(){
        $statuses = [];

        foreach($this->container as $key => $status){
            $statuses[] = $this->offsetGet($key);
        }

        return $statuses;
    }


    public function __invoke($value){
        if( !isset($this->default) ){
            $this->default = $value;
            return true;
        }else{
            return false;
        }
    }


    public function __toString(){
        return (string)$this->default;
    }
}