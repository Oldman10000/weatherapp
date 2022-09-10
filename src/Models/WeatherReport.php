<?php

namespace Oldman10000\WeatherApp\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherReport extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'weather_report';

    public function ipAddress()
    {
        return $this->hasOne(IpAddress::class);
    }
}
