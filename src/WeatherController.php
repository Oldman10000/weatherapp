<?php

namespace Oldman10000\WeatherApp;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Oldman10000\WeatherApp\Models\IpAddress;
use Oldman10000\WeatherApp\Models\WeatherReport;
use WeakMap;

class WeatherController extends Controller
{
    // usually this would be defined in env file for security reasons
    // it can be a class constant
    const ACCUWEATHER_API_KEY = 'Lw1JN6Ug100suJAtqvTMJpeM1FlooHl3';

    public function index($weatherReport = null)
    {
        // get user ip to prefill form field
        // note that in dev environment this will just be localhost / 127.0.0.1
        // so just make it null if thats the case as it isn't of any use here
        $clientIp = '127.0.0.1' ? null : request()->ip();

        // if the ip address form is submitted we can get the submitted data here
        $requestIp = request('ip');

        // if the ip address is in the request we need to get the weather data
        if ($requestIp) {
            // check if the ip exists in the ip address table
            $checker = IpAddress::where('name', '=', $requestIp)->first();

            // if not, then we add it!
            if (!$checker) {
                $ipAddress = new IpAddress();
                $ipAddress->name = $requestIp;
                $ipAddress->save();
            }

            // check whether this ip already has a weather report from today
            // to avoid making too many api calls then we can use this data rather than create a new one
            $weatherReport = DB::table('weather_report')
                ->join(
                    'allowed_ip_addresses',
                    'allowed_ip_addresses.id',
                    '=',
                    'weather_report.ip_address_id'
                )
                ->where('allowed_ip_addresses.name', '=', $requestIp)
                ->whereDate('weather_report.created_at', Carbon::today())
                ->select('weather_report.*')
                ->first();

            // there is no data from today so we have to create a new one using the api
            if (!$weatherReport) {
                // we store the weather data in its own table
                $weatherReport = $this->storeWeatherReport($requestIp);
            }

            // now we redirect to weather view which shows weather data
            return redirect('/weather/' . $weatherReport->id);
        }

        // get all ip addresses in database to use as form options
        $ipAddresses = IpAddress::all();

        $parameters = [
            'ipAddresses' => $ipAddresses,
            'requestIp' => $requestIp,
            'clientIp' => $clientIp,
        ];

        return view('weather::index', $parameters);
    }

    public function show($weatherReportId)
    {
        // get the weather report based on the url parameter
        $weatherReport = WeatherReport::find($weatherReportId);

        // turn json weather data into php array
        $weatherData = json_decode($weatherReport->weather_data);

        $parameters = [
            'weatherData' => $weatherData,
        ];

        return view('weather::weather', $parameters);
    }

    public function storeWeatherReport($requestIp)
    {
        // get the json formatted weather data from the accuweather api
        $weatherData = $this->getWeatherData($requestIp);

        // add new weather report to database adding the ip address and weather data
        $weatherReport = new WeatherReport();
        $weatherReport->ip_address_id = IpAddress::where(
            'name',
            '=',
            $requestIp
        )->first()->id;
        $weatherReport->weather_data = $weatherData;
        $weatherReport->save();

        return $weatherReport;
    }

    public function getWeatherData($ipAddress)
    {
        // need to call the weather api twice, once to retrieve the location key using the ip address
        // then again using the location key to retrieve the weather data

        $url =
            'http://dataservice.accuweather.com/locations/v1/cities/ipaddress?apikey=' .
            self::ACCUWEATHER_API_KEY .
            '&q=' .
            $ipAddress .
            '&details=true';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        $result = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($result);

        $locationKey = $result->Details->CanonicalLocationKey;

        // now we have the location key we get the actual weather data

        $url =
            'http://dataservice.accuweather.com/forecasts/v1/daily/5day/' .
            $locationKey .
            '?apikey=' .
            self::ACCUWEATHER_API_KEY;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }
}
