<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    //
    protected $table = 'payment';
    public $timestamps = false;

    protected $fillable = [
        'authorizationCode',
        'creditCardType',
        'last4CardDigits',
        'responseCode',
        'transactionId',
    ];

    public function salesOrder()
    {
        return $this->hasOne('App\SalesOrder');
    }
}
