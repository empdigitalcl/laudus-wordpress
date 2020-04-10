<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use DB;

class Location extends Model
{
    protected $fillable = [
        'countryId',
        'locationId',
        'weight',
        'address',
        'name',
        'code',
        'isVisible'
      ];
    protected $table = 'location';
    public $timestamps = false;

    public function children()
    {
        return $this->hasMany('App\Location', 'locationId');
    }

    public function father()
    {
        return $this->belongsTo('App\Location', 'locationId');
    }
    /* public function grandChildren()
    {
        return $this->children()->with('grandChildren');
    } */

    public function scopeOnlyFathers($query)
    {
        return $query->whereNull('location.locationId');
    }
    public function scopeOrderByWeight($query)
    {
        return $query->orderBy('location.weight', 'ASC');
    }
    public function scopeVisible($query)
    {
        return $query->where('location.isVisible', '1');
    }
    public function scopeGrandChildren($query, $locationId)
    {
        return $query
            ->select('locGC.*')
            ->join('location as loc', 'loc.locationId', '=', DB::raw($locationId))
            ->join('location as locGC', 'locGC.locationId', '=', 'loc.id')
            ->orderBy('locGC.name', 'ASC')
            ->groupBy('locGC.id');
    }


}
