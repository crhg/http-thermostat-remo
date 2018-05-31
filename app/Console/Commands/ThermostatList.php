<?php

namespace App\Console\Commands;

use Crhg\RemoClient\Api\DefaultApi;
use Crhg\RemoClient\Model\AirConRange;
use Crhg\RemoClient\Model\AirConRangeMode;
use Crhg\RemoClient\Model\ApplianceType;
use Illuminate\Console\Command;

class ThermostatList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'thermostat:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List thermostats';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Crhg\RemoClient\ApiException
     */
    public function handle()
    {
        /** @var DefaultApi $api */
        $api = app()->make(DefaultApi::class);

        $appliances = $api->appliancesGet();

        $header = ['Id', 'Name', 'MinTemp', 'MaxTemp'];

        $list = [];
        foreach ($appliances as $a) {
            if ($a->getType() !== ApplianceType::AC) {
                continue;
            }

            $modes = $a->getAircon()->getRange()->getModes();

            $temps = [];
            foreach (['cool', 'warm'] as $mode) {
                $temps = array_merge($temps, $modes[$mode]->getTemp());
            }

            $minTemp = min($temps);
            $maxTemp = max($temps);

            $list []= [
                $a->getId(),
                $a->getNickname(),
                $minTemp,
                $maxTemp,
            ];

            $this->table($header, $list);
        }
    }
}
