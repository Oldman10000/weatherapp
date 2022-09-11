<?php

namespace Oldman10000\WeatherApp\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Oldman10000\WeatherApp\Models\IpAddress;
use Oldman10000\WeatherApp\WeatherController;

class GetWeatherData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:weather-data {ip}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Return 5 day weather report based on IP address';

    /**
     * Create a new command instance.
     */
    public function __construct(
        private WeatherController $weatherController,
    )
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $addRecord = $this->argument('ip');

        // check if the ip exists in the ip address table
        $ipAddress = IpAddress::where('name', '=', $addRecord)->first();

        // if not, then we add it!
        if (!$ipAddress) {
            $ipAddress = $this->weatherController->storeIpAddress($addRecord);
        } elseif (!$ipAddress->location_data) {
            // if the ip address has no location data we need to add it
            $locationData = $this->weatherController->getLocationData(
                $addRecord
            );
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
            ->where('allowed_ip_addresses.name', '=', $addRecord)
            ->whereDate('weather_report.created_at', Carbon::today())
            ->select('weather_report.*')
            ->first();

        // there is no data from today so we have to create a new one using the api
        if (!$weatherReport) {
            $locationData = json_decode($ipAddress->location_data);
            $locationKey = $locationData->Key;
            // we store the weather data in its own table
            $weatherReport = $this->weatherController->storeWeatherReport(
                $ipAddress,
                $locationKey
            );
        }

        $weatherData = json_decode($weatherReport->weather_data);

        echo('Headline - ' . $weatherData->Headline->Text . ' ' . date('j F, Y', strtotime($weatherData->Headline->EffectiveDate)) . PHP_EOL);

        foreach ($weatherData->DailyForecasts as $forecast) {
            $date = date('j F, Y', strtotime($forecast->Date));
            echo($date . ' - ' . $forecast->Day->IconPhrase . PHP_EOL);
        }

    }
}
