<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $fillable = [
        'personId',
        'paymentMethodId',
        'status',
        'date',
        'total',
    ];
    protected $table = 'salesOrder';
    public $timestamps = false;

    public function paymentMethod()
    {
        return $this->belongsTo('App\PaymentMethod', 'paymentMethodId');
    }
    public function plan()
    {
        return $this->belongsTo('App\Plan', 'planId');
    }
    public function subscription()
    {
        return $this->hasOne('App\Subscription', 'salesOrderId');
    }
    public function payment()
    {
        return $this->hasOne('App\Payment', 'salesOrderId');
    }
    public function person()
    {
        return $this->belongsTo('App\Person', 'personId');
    }
    public function scopeByPerson($query, $personId)
    {
        if ($personId != null && $personId != '') {
            return $query->where('personId', $pid);
        }
    }
    public function scopeByWebpayToken($query, $webpayToken)
    {
        if ($webpayToken != null && $webpayToken != '') {
            return $query->where('webpayToken', $webpayToken);
        }
    }

    public function scopeOrderByDesc($query)
    {
        return $query->orderBy('timeStampAdd', 'DESC');
    }
}
