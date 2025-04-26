<?php

declare(strict_types=1);

namespace Vkarchevskyi\SinoptikUaParser\DataTransferObjects;

use Vkarchevskyi\SinoptikUaParser\Services\Localization\EmojiService;

readonly class WeatherData
{
    public function __construct(
        public string $description,
        public string $temperature,
        public string $feelsLike,
        public string $pressure,
        public string $humidity,
        public string $wind,
        public string $precipitationProbability
    ) {
    }

    public function getEmoji(): string
    {
        return new EmojiService()->get($this->description) ?? '';
    }
}
