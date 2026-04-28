<?php

declare(strict_types=1);

namespace Vkarchevskyi\SinoptikUaParser\Builders;

use Vkarchevskyi\SinoptikUaParser\DataTransferObjects\WeatherPeriodData;

final class WeatherPeriodDataBuilder
{
    private WeatherDataBuilder $weatherDataBuilder;

    public function __construct(private string $time)
    {
        $this->weatherDataBuilder = new WeatherDataBuilder();
    }

    public function getWeatherDataBuilder(): WeatherDataBuilder
    {
        return $this->weatherDataBuilder;
    }

    public function make(): WeatherPeriodData
    {
        return new WeatherPeriodData(
            $this->time,
            $this->weatherDataBuilder->make(),
        );
    }
}
