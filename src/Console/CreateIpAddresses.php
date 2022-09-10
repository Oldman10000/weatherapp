<?php

namespace Oldman10000\WeatherApp\Console;

use Illuminate\Console\Command;
use Oldman10000\WeatherApp\Models\IpAddress;

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
    public function __construct()
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
        $ipAddress = new IpAddress();
        $ipAddress->name = $addRecord;
        $ipAddress->save();
    }
}
