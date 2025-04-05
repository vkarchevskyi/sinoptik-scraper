<?php

declare(strict_types=1);

namespace Vkarchevskyi\SinoptikUaParser\DataTransferObjects;

readonly class WeatherData
{
    public function __construct(
        public string $temperature,
        public string $feelsLike,
        public string $pressure,
        public string $humidity,
        public string $wind,
        public string $precipitationProbability
    ) {
    }
}