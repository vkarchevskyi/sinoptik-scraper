<?php

declare(strict_types=1);

namespace Vkarchevskyi\SinoptikUaParser\Builders;

use Vkarchevskyi\SinoptikUaParser\DataTransferObjects\WeatherData;
use Vkarchevskyi\SinoptikUaParser\Enums\WeatherProperty;

final class WeatherDataBuilder
{
    private string $description = '';
    private string $temperature = '';
    private string $feelsLike = '';
    private string $pressure = '';
    private string $humidity = '';
    private string $wind = '';
    private string $precipitationProbability = '';
    private ?string $code = null;

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function setTemperature(string $temperature): self
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function setFeelsLike(string $feelsLike): self
    {
        $this->feelsLike = $feelsLike;

        return $this;
    }

    public function setPressure(string $pressure): self
    {
        $this->pressure = $pressure;

        return $this;
    }

    public function setHumidity(string $humidity): self
    {
        $this->humidity = $humidity;

        return $this;
    }

    public function setWind(string $wind): self
    {
        $this->wind = $wind;

        return $this;
    }

    public function setPrecipitationProbability(string $precipitationProbability): self
    {
        $this->precipitationProbability = $precipitationProbability;

        return $this;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function setByProperty(WeatherProperty $property, string $value): self
    {
        return match ($property) {
            WeatherProperty::Description => $this->setDescription($value),
            WeatherProperty::Temperature => $this->setTemperature($value),
            WeatherProperty::FeelsLike => $this->setFeelsLike($value),
            WeatherProperty::Pressure => $this->setPressure($value),
            WeatherProperty::Humidity => $this->setHumidity($value),
            WeatherProperty::Wind => $this->setWind($value),
            WeatherProperty::PrecipitationProbability => $this->setPrecipitationProbability($value),
        };
    }

    public function make(): WeatherData
    {
        return new WeatherData(
            $this->description,
            $this->temperature,
            $this->feelsLike,
            $this->pressure,
            $this->humidity,
            $this->wind,
            $this->precipitationProbability,
            $this->code,
        );
    }
}
