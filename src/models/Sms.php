<?php

namespace HayriCan\CodecFastSms\Models;

use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{

    /**
     * @var string
     */
    protected $table = 'sms_records';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'msgSpecialId', 'isOtn', 'headerCode', 'phone', 'messageContent'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}