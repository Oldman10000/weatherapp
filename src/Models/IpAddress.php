<?php

namespace Oldman10000\WeatherApp\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpAddress extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'allowed_ip_addresses';

    public function weatherReport()
    {
        return $this->belongsTo(
            WeatherReport::class,
            'foreign_key',
            'owner_key'
        );
    }
}
