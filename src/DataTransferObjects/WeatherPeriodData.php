<?php

declare(strict_types=1);

namespace Vkarchevskyi\SinoptikUaParser\DataTransferObjects;

readonly class WeatherPeriodData
{
    /**
     * @param array<string, string> $data
     */
    public function __construct(
        public string $time,
        public array $data,
    ) {
    }
}
