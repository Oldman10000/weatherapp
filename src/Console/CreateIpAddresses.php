<?php

namespace Oldman10000\WeatherApp\Console;

use Illuminate\Console\Command;
use Oldman10000\WeatherApp\Models\IpAddress;
use Oldman10000\WeatherApp\WeatherController;

class CreateIpAddresses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:insert-ipaddresses {ip}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds new ip addresses to the ip addresses table';

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

        if (!$ipAddress) {
            $ipAddress = new IpAddress();
            $ipAddress->name = $addRecord;
            $locationData = $this->weatherController->getLocationData(
                $addRecord
            );
            $ipAddress->location_data = $locationData;
            $ipAddress->save();
        } else {
            echo('A record for ' . $addRecord . ' already exists!');
        }
    }
}
