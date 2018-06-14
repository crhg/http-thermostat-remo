<?php
/**
 * Created by IntelliJ IDEA.
 * User: matsui
 * Date: 2018/05/31
 * Time: 10:46
 */

namespace App;


use Crhg\RemoClient\Api\DefaultApi;
use Crhg\RemoClient\Model\AirConParams;
use Crhg\RemoClient\Model\Appliance;
use Crhg\RemoClient\Model\ApplianceType;
use Crhg\RemoClient\Model\Button;
use Crhg\RemoClient\Model\Device;
use Crhg\RemoClient\Model\OperationMode;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Thermostat
{
    const STATUS_CACHE_KEY_PREFIX = 'status:';
    const EXPIRE = 5; // minutes

    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    private function getStatusCacheKey(): string
    {
        return self::STATUS_CACHE_KEY_PREFIX . $this->id;
    }

    /**
     * @return array
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function getStatus(): array
    {
        return Cache::remember(
            $this->getStatusCacheKey(),
            self::EXPIRE,
            function () {
                return $this->getStatusRaw();
            });
    }

    /**
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \Crhg\RemoClient\ApiException
     */
    private function getStatusRaw(): array
    {
        /** @var DefaultApi $api */
        $api = app()->make(DefaultApi::class);

        /** @var Appliance[] $appliances */
        $appliances = $api->appliancesGet();
        /** @var Appliance $aircon */
        $aircon = array_first($appliances, function (Appliance $a) {
            return $a->getId() === $this->id;
        });
        if ($aircon === null) {
            throw new NotFoundHttpException($this->id . ' not found');
        }
        if ($aircon->getType() !== ApplianceType::AC) {
            throw new NotFoundHttpException($this->id . ' is not a thermostat');
        }

        $settings = $aircon->getSettings();
        $temp = $settings->getTemp();
        $state = $this->convertHeatingCoolingState($settings);

        /** @var string $device_id */
        $device_id = $aircon->getDevice()->getId();
        $devices = $api->devicesGet();
        /** @var Device $device */
        $device = array_first($devices, function (Device $d) use ($device_id) {
            return $d->getId() === $device_id;
        });
        if ($device === null) {
            throw new \RuntimeException("device not found: $device_id for " . $this->id);
        }

        $events = $device->getNewestEvents();
        $te = $events->getTe();
        $current_te = $te->getVal();
        $hu = $events->getHu();
        $current_hu = $hu->getVal();

        $result = [
            'targetHeatingCoolingState' => 0 + $state,
            'targetTemperature' => 0.0 + $temp,
            'targetRelativeHumidity' => 0.0 + 50.0,
            'currentHeatingCoolingState' => 0 + $state,
            'currentTemperature' => 0.0 + $current_te,
            'currentRelativeHumidity' => 0.0 + $current_hu,
        ];

        return $result;
    }

    private function convertHeatingCoolingState(AirConParams $settings): int
    {
        if ($settings->getButton() === Button::POWER_OFF) {
            return 0;
        }

        if ($settings->getMode() === OperationMode::WARM) {
            return 1;
        }

        return 2;
    }

    /**
     * @param $mode
     * @param $button
     * @throws \InvalidArgumentException
     * @throws \Crhg\RemoClient\ApiException
     */
    public function setStatus($mode, $button)
    {
        /** @var DefaultApi $api */
        $api = app()->make(DefaultApi::class);

        $api->appliancesApplianceAirconSettingsPost($this->id, null, $mode, null, null, $button);

        Cache::forget($this->getStatusCacheKey());
    }

    /**
     * @param $temp
     * @throws \InvalidArgumentException
     * @throws \Crhg\RemoClient\ApiException
     */
    public function setTemperature($temp)
    {
        /** @var DefaultApi $api */
        $api = app()->make(DefaultApi::class);

        $api->appliancesApplianceAirconSettingsPost($this->id, $temp);

        Cache::forget($this->getStatusCacheKey());
    }
}