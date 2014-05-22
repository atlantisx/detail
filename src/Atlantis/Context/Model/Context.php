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

}