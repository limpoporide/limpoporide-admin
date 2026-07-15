<?php

/**
 * Car Type Model
 *
 * @package     NewTaxi
 * @subpackage  Model
 * @category    Car Type
 * @author      Seen Technologies
 * @version     2.2.1
 * @link        https://seentechs.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'vehicle_type';
 
    public $timestamps = false;

    /**
     * get Vehicle Image Attribute
     *
     */
    public function getVehicleImageAttribute()
    {
        return url('vehicle_image/vehicle_type_image/'.$this->attributes['image']);   
    }

    /**
     * get Vehicle Active Image Attribute
     * 
     */
    // public function getActiveImageAttribute()
    // {
    //     $url = \App::runningInConsole() ? SITE_URL : url('/');
    //     return $url.'/vehicle_image/vehicle_type_image/'.$this->attributes['active_image'];   
          
    // }
 
    /**
     * Scope to get Active Records Only 
     *
     */
    public function scopeActive($query)
    {
        return $query->whereStatus('Active');
    }

    /** 
     * Join with fare table
     *
     */
    public function manage_fare()
    {
        return $this->belongsTo('App\Models\ManageFare','id','vehicle_id');
    }

}
