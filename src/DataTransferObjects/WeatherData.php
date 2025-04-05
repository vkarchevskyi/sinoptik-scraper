<?php

declare(strict_types=1);

namespace Vkarchevskyi\SinoptikUaParser\DataTransferObjects;

readonly class WeatherData
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
