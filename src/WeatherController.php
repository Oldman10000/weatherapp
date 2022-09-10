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
            $ipAddress = IpAddress::where('name', '=', $requestIp)->first();

            // if not, then we add it!
            if (!$ipAddress) {
                $ipAddress = $this->storeIpAddress($requestIp);
            } elseif (!$ipAddress->location_data) {
                // if the ip address has no location data we need to add it
                $locationData = $this->getLocationData($requestIp);
                $ipAddress->location_data = $locationData;
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
                $locationData = json_decode($ipAddress->location_data);
                $locationKey = $locationData->Key;
                // we store the weather data in its own table
                $weatherReport = $this->storeWeatherReport(
                    $ipAddress,
                    $locationKey
                );
            }

            // now we redirect to weather view which shows weather data
            return redirect('/weather/' . $weatherReport->id);
        }

        // get all ip addresses in database to use as form options
        $ipAddresses = IpAddress::all();

        $headerText = 'Get your 5 day weather report using your Ip Address';

        $parameters = [
            'ipAddresses' => $ipAddresses,
            'requestIp' => $requestIp,
            'clientIp' => $clientIp,
            'headerText' => $headerText,
        ];

        return view('weather::index', $parameters);
    }

    public function show($weatherReportId)
    {
        // get the weather report based on the url parameter
        $weatherReport = WeatherReport::find($weatherReportId);

        // turn json weather data into php array
        $weatherData = json_decode($weatherReport->weather_data);

        $ipAddress = IpAddress::find($weatherReport->ip_address_id);
        $locationData = json_decode($ipAddress->location_data);

        $headerText =
            'Your 5 day weather forecast in ' .
            $locationData->EnglishName .
            ', ' .
            $locationData->Country->EnglishName;

        $parameters = [
            'weatherData' => $weatherData,
            'locationData' => $locationData,
            'headerText' => $headerText,
        ];

        return view('weather::weather', $parameters);
    }

    public function storeIpAddress($requestIp)
    {
        $locationData = $this->getLocationData($requestIp);

        $ipAddress = new IpAddress();
        $ipAddress->name = $requestIp;
        $ipAddress->location_data = $locationData;
        $ipAddress->save();

        return $ipAddress;
    }

    public function storeWeatherReport($ipAddress, $locationKey)
    {
        // get the json formatted weather data from the accuweather api
        $weatherData = $this->getWeatherData($locationKey);

        // add new weather report to database adding the ip address and weather data
        $weatherReport = new WeatherReport();
        $weatherReport->ip_address_id = $ipAddress->id;
        $weatherReport->weather_data = $weatherData;
        $weatherReport->save();

        return $weatherReport;
    }

    public function getLocationData($requestIp)
    {
        // use the ip address to get the ip address json data which includes the location key used to get the weather data
        $url =
            'http://dataservice.accuweather.com/locations/v1/cities/ipaddress?apikey=' .
            self::ACCUWEATHER_API_KEY .
            '&q=' .
            $requestIp .
            '&details=true';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }

    public function getWeatherData($locationKey)
    {
        // get weather data using the location key

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
