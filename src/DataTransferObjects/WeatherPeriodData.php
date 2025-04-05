<?php

declare(strict_types=1);

namespace Vkarchevskyi\SinoptikUaParser\DataTransferObjects;

readonly class WeatherPeriodData
{
    public function __construct(
        public string $time,
        public WeatherData $data,
    ) {
    }
}
