<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $table = 'person';
    public $timestamps = false;

    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'code',
        'status',
    ];
    public function user()
    {
        return $this->belongsTo('App\Person', 'userId');
    }
    public function scopeByUserId($query, $userId)
    {
        return $query->where('userId', $userId);
    }
    public function salesOrders()
    {
        return $this->hasMany('App\SalesOrder', 'personId');
    }

}
