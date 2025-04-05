<?php

declare(strict_types=1);

namespace Vkarchevskyi\SinoptikUaParser\Services;

use DateTimeImmutable;
use DateTimeZone;
use RuntimeException;
use Vkarchevskyi\SinoptikUaParser\DataTransferObjects\WeatherData;

class CurrentTimeIndexService
{
    /**
     * @param array<int, WeatherData> $data
     *
     * @throws RuntimeException
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
        $index = key($intervals);

        if (!is_int($index)) {
            throw new RuntimeException("Index must be an integer. $index given.");
        }

        return $index;
    }
}