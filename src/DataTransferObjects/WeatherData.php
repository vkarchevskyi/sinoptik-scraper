<?php

declare(strict_types=1);

namespace Vkarchevskyi\SinoptikUaParser\DataTransferObjects;

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
        return match ($this->description) {
            // 🌩 Thunderstorm (with or without snow)
            "Невелика хмарність,\nможливий мокрий сніг, грози",
            "Невелика хмарність,\nсильний сніг, можливі грози",
            "Мінлива хмарність,\nможливий мокрий сніг, грози",
            "Мінлива хмарність,\nсильний сніг, можливі грози",
            "Хмарно з проясненнями,\nмокрий сніг, грози",
            "Хмарно з проясненнями,\nсильний сніг, грози",
            "Суцільна хмарність,\nмокрий сніг, грози",
            "Суцільна хмарність,\nсильний сніг, грози",
            "Невелика хмарність,\nдощ, можливі грози",
            "Мінлива хмарність,\nдощ, можливі грози",
            "Хмарно з проясненнями,\nдощ, грози",
            "Суцільна хмарність,\nдощ, грози" => "\u{1F329}",

            // 🌨 Snow
            "Невелика хмарність,\nможливий мокрий сніг",
            "Невелика хмарність,\nможливий сніг",
            "Невелика хмарність,\nможливий сильний сніг",
            "Невелика хмарність,\nможливий дрібний мокрий сніг",
            "Мінлива хмарність,\nсніг",
            "Мінлива хмарність,\nдощ зі снігом",
            "Мінлива хмарність,\nдрібний мокрий сніг",
            "Мінлива хмарність,\nмокрий сніг",
            "Мінлива хмарність,\nневеликий сніг",
            "Мінлива хмарність,\nсильний сніг",
            "Хмарно з проясненнями,\nсніг",
            "Хмарно з проясненнями,\nдощ зі снігом",
            "Хмарно з проясненнями,\nневеликий сніг",
            "Хмарно з проясненнями,\nмокрий сніг",
            "Хмарно з проясненнями,\nдрібний мокрий сніг",
            "Хмарно з проясненнями,\nсильний сніг",
            "Суцільна хмарність,\nсніг",
            "Суцільна хмарність,\nневеликий сніг",
            "Суцільна хмарність,\nмокрий сніг",
            "Суцільна хмарність,\nдрібний мокрий сніг",
            "Суцільна хмарність,\nсильний сніг" => "\u{1F328}",

            // 🌧 Rain
            "Невелика хмарність,\nможливий дрібний дощ",
            "Невелика хмарність,\nможливий дощ",
            "Невелика хмарність,\nсильний дощ",
            "Мінлива хмарність,\nдрібний дощ",
            "Мінлива хмарність,\nдощ",
            "Мінлива хмарність,\nсильний дощ",
            "Хмарно з проясненнями,\nдрібний дощ",
            "Хмарно з проясненнями,\nдощ",
            "Хмарно з проясненнями,\nсильний дощ",
            "Суцільна хмарність,\nдрібний дощ",
            "Суцільна хмарність,\nдощ",
            "Суцільна хмарність,\nсильний дощ",
            "Суцільна хмарність,\nдощ зі снігом" => "\u{1F327}",

            // ☁️ Cloudy
            "Суцільна хмарність" => "\u{2601}\u{FE0F}",

            // 🌤 Partly cloudy (no precipitation)
            "Невелика хмарність",
            "Мінлива хмарність",
            "Хмарно з проясненнями" => "\u{1F324}",

            // ☀️ Clear
            "Ясно" => "\u{2600}\u{FE0F}",

            // 🌫 Fog
            "Туман" => "\u{1F32B}",

            // 🌁 High clouds
            "Невеликі високі хмари" => "\u{1F301}",

            // 🧊 Hail
            "Град" => "\u{1F9CA}",

            default => "",
        };
    }
}
