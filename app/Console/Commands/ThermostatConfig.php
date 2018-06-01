<?php /** @noinspection PhpInconsistentReturnPointsInspection */

namespace App\Console\Commands;

use Crhg\RemoClient\Api\DefaultApi;
use Crhg\RemoClient\Model\ApplianceType;
use Illuminate\Console\Command;

class ThermostatConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'thermostat:config {base_uri?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Output json fragment to configure homebridge';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \InvalidArgumentException
     * @throws \Crhg\RemoClient\ApiException
     */
    public function handle()
    {
        $base_uri = $this->argument('base_uri') ?? 'http://example.net';

        /** @var DefaultApi $api */
        $api = app()->make(DefaultApi::class);

        $appliances = $api->appliancesGet();

        $accessories = [];
        foreach ($appliances as $a) {
            if ($a->getType() !== ApplianceType::AC) {
                continue;
            }

            $modes = $a->getAircon()->getRange()->getModes();

            $temps = [];
            foreach (['cool', 'warm'] as $mode) {
                $temps [] = $modes[$mode]->getTemp();
            }

            /** @noinspection SlowArrayOperationsInLoopInspection */
            $temps = array_merge(...$temps);

            $minTemp = min($temps);
            $maxTemp = max($temps);

            /** @var string $id */
            $id = $a->getId();
            $accessories [] = [
                'accessory' => 'Thermostat',
                'name' => $a->getNickname(),
                'apiroute' => $base_uri.'/api/thermostat/'.$id,
                'maxTemp' => 0 + $maxTemp,
                'minTemp' => 0 + $minTemp,
            ];
        }

        $this->line(json_encode(['accessories' => $accessories], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
    }
}
