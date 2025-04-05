<?php

declare(strict_types=1);

namespace Vkarchevskyi\SinoptikUaParser\Services;

use DateTimeImmutable;
use DateTimeZone;
use Vkarchevskyi\SinoptikUaParser\DataTransferObjects\WeatherData;

class CurrentTimeIndexService
{
    /**
     * @param list<WeatherData> $data
     */
    public function get(array $data, DateTimeImmutable $date): int
    {
        $intervals = [];

        foreach ($data as $dataPerTime) {
            [$hours, $minutes] = explode(':', $dataPerTime->time);

            $dateTimeFromTime = new DateTimeImmutable()
                ->setTimezone(new DateTimeZone('Europe/Kyiv'))
                ->setTime((int)$hours, (int)$minutes);

            $intervals[] = abs($date->getTimestamp() - $dateTimeFromTime->getTimestamp());
        }

        asort($intervals);

        return key($intervals);
    }
}