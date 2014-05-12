<?php namespace Atlantis\Context\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;


class Context extends Eloquent{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'contexts';


    public function detail(){
        return $this->belongsTo('\Atlantis\Detail\Model\Detail');
    }


    public function _load(){
        $contexts = [
            'route_application' => [
                'condition' => [
                    'provider'  => 'route_is',
                    'parameters' => 'application'
                ],
                'reaction' => [
                    'provider' => 'attribute_set',
                    'parameters' => [
                        'name'  => 'status_actions',
                        'value' => array(0,1,2)
                    ]
                ]
            ],
            'route_application_review' => [
                'condition' => [
                    'provider'  => 'route_is',
                    'parameters' => 'application.review'
                ],
                'reaction' => [
                    'provider' => 'attribute_set',
                    'parameters' => [
                        'name'  => 'status_actions',
                        'value' => array(1,3)
                    ]
                ]
            ],
            'route_application_approve' => [
                'condition' => [
                    'provider'  => 'route_is',
                    'parameters' => 'application.approve'
                ],
                'reaction' => [
                    'provider' => 'attribute_set',
                    'parameters' => [
                        'name'  => 'status_actions',
                        'value' => array(4,5,6,7,8)
                    ]
                ]
            ],
            'route_application_payment' => [
                'condition' => [
                    'provider'  => 'route_is',
                    'parameters' => 'application.payment'
                ],
                'reaction' => [
                    'provider' => 'attribute_set',
                    'parameters' => [
                        'name'  => 'status_actions',
                        'value' => array(7)
                    ]
                ]
            ]

        ];
    }


    public function getConditionsAttribute($value){
        if( json_decode($value) ) {
            return json_decode($value);
        }else{
            return $value;
        }
    }

    public function setConditionsAttribute($value){
        $this->attributes['condition'] = json_encode($value);
    }


    public function getReactionsAttribute($value){
        if( json_decode($value) ) {
            return json_decode($value);
        }else{
            return $value;
        }
    }

    public function setReactionsAttribute($value){
        $this->attributes['reactions'] = json_encode($value);
    }

}