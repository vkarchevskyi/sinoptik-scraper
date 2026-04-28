<?php

declare(strict_types=1);

namespace Vkarchevskyi\SinoptikUaParser\Enums;

enum WeatherProperty: int
{
    case Description = 0;
    case Temperature = 1;
    case FeelsLike = 2;
    case Pressure = 3;
    case Humidity = 4;
    case Wind = 5;
    case PrecipitationProbability = 6;

    public function key(): string
    {
        return match ($this) {
            self::Description => 'description',
            self::Temperature => 'temperature',
            self::FeelsLike => 'feelsLike',
            self::Pressure => 'pressure',
            self::Humidity => 'humidity',
            self::Wind => 'wind',
            self::PrecipitationProbability => 'precipitationProbability',
        };
    }
}
