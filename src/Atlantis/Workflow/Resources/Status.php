<?php namespace Atlantis\Workflow\Resources;

use ArrayAccess;
use Atlantis\View\Interfaces\Realm;


class Status implements ArrayAccess{
    private $container = [];
    protected $default = null;
    protected $realm;

    protected $status = [
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
            -1 => 'in_process'
        ]
    ];

    protected $label = array(
        -1 => 'warning',
        0 => 'info',
        1 => 'warning',
        2 => 'danger',
        3 => 'success',
        4 => 'success',
        5 => 'danger',
        6 => 'warning',
        7 => 'success',
        8 => 'danger',
        9 => 'success',
        12=> 'success'
    );

    protected $colour = array(
        -1 => 'orange',
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


    public function __construct(Realm $realm){
        #i: Get current realm
        $this->realm = $realm->current();

        #i: Set as default arrays
        $this->container = $this->realm ? $this->status[$this->realm->name] : $this->status['staff'];
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
        $realm_name = $this->realm ? $this->realm->name : 'staff';
        $offset = $this->realm ? $this->offsetRealm($this->realm->name,$offset) : $offset;

        #i: Get value
        if( isset($this->container[$offset]) ){
            return [
                'id'    => $offset,
                'name'  => $this->container[$offset],
                'title' => trans("advance::advance.status.{$realm_name}.".$this->container[$offset]),
                'label' => $this->label[$offset],
                'color' => $this->colour[$offset]
            ];
        }else{
            return [
                'id'    => $offset,
                'name'  => $this->status['staff'][$offset],
                'title' => trans("advance::advance.status.staff.".$this->status['staff'][$offset]),
                'label' => $this->label[$offset],
                'color' => $this->colour[$offset]
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


    public function offsetRealm($realm,$offset){
        /** Student status override */
        if($realm == 'student'){
            $found = null;
            $status_student = array(
                -1 => array(3,4,5,6)
            );

            foreach( $status_student as $key => $value ){
                if( in_array($offset, $value) ){
                    $found = $key;
                }
            }

            if($found) return $found;
        }

        return $offset;
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