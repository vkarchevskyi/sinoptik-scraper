<?php

declare(strict_types=1);

namespace Vkarchevskyi\SinoptikUaParser\Services\Localization;

use Vkarchevskyi\SinoptikUaParser\Enums\Language;

final class WeatherTranslationService
{
    private const array CODE_TO_UA = [
        '0' => "Ясно",
        '100' => "Невелика хмарність",
        '103' => "Град",
        '110' => "Невелика хмарність,\nможливий дрібний дощ",
        '111' => "Невелика хмарність,\nможливий дрібний мокрий сніг",
        '112' => "Невелика хмарність,\nможливий сніг",
        '120' => "Невелика хмарність,\nможливий дощ",
        '121' => "Невелика хмарність,\nможливий мокрий сніг",
        '122' => "Невелика хмарність,\nможливий сніг",
        '130' => "Невелика хмарність,\nсильний дощ",
        '131' => "Невелика хмарність,\nможливий мокрий сніг",
        '132' => "Невелика хмарність,\nможливий сильний сніг",
        '140' => "Невелика хмарність,\nдощ, можливі грози",
        '141' => "Невелика хмарність,\nможливий мокрий сніг, грози",
        '142' => "Невелика хмарність,\nсильний сніг, можливі грози",
        '200' => "Мінлива хмарність",
        '210' => "Мінлива хмарність,\nдрібний дощ",
        '211' => "Мінлива хмарність,\nдрібний мокрий сніг",
        '212' => "Мінлива хмарність,\nневеликий сніг",
        '220' => "Мінлива хмарність,\nдощ",
        '221' => "Мінлива хмарність,\nдощ зі снігом",
        '222' => "Мінлива хмарність,\nсніг",
        '230' => "Мінлива хмарність,\nсильний дощ",
        '231' => "Мінлива хмарність,\nмокрий сніг",
        '232' => "Мінлива хмарність,\nсильний сніг",
        '240' => "Мінлива хмарність,\nдощ, можливі грози",
        '241' => "Мінлива хмарність,\nможливий мокрий сніг, грози",
        '242' => "Мінлива хмарність,\nсильний сніг, можливі грози",
        '300' => "Хмарно з проясненнями",
        '310' => "Хмарно з проясненнями,\nдрібний дощ",
        '311' => "Хмарно з проясненнями,\nдрібний мокрий сніг",
        '312' => "Хмарно з проясненнями,\nневеликий сніг",
        '320' => "Хмарно з проясненнями,\nдощ",
        '321' => "Хмарно з проясненнями,\nдощ зі снігом",
        '322' => "Хмарно з проясненнями,\nсніг",
        '330' => "Хмарно з проясненнями,\nсильний дощ",
        '331' => "Хмарно з проясненнями,\nмокрий сніг",
        '332' => "Хмарно з проясненнями,\nсильний сніг",
        '340' => "Хмарно з проясненнями,\nдощ, грози",
        '341' => "Хмарно з проясненнями,\nмокрий сніг, грози",
        '342' => "Хмарно з проясненнями,\nсильний сніг, грози",
        '400' => "Суцільна хмарність",
        '410' => "Суцільна хмарність,\nдрібний дощ",
        '411' => "Суцільна хмарність,\nдрібний мокрий сніг",
        '412' => "Суцільна хмарність,\nневеликий сніг",
        '420' => "Суцільна хмарність,\nдощ",
        '421' => "Суцільна хмарність,\nдощ зі снігом",
        '422' => "Суцільна хмарність,\nсніг",
        '430' => "Суцільна хмарність,\nсильний дощ",
        '431' => "Суцільна хмарність,\nмокрий сніг",
        '432' => "Суцільна хмарність,\nсильний сніг",
        '440' => "Суцільна хмарність,\nдощ, грози",
        '441' => "Суцільна хмарність,\nмокрий сніг, грози",
        '442' => "Суцільна хмарність,\nсильний сніг, грози",
        '500' => "Невеликі високі хмари",
        '600' => "Туман",
    ];

    private const array CODE_TO_EN = [
        '0' => "Clear",
        '100' => "Cloudy",
        '103' => "Hail",
        '110' => "Cloudy.\nLight Showers",
        '111' => "Cloudy.\nSleet",
        '112' => "Cloudy chance of Snow",
        '120' => "Cloudy chance of rain",
        '121' => "Cloudy chance of Sleet",
        '122' => "Cloudy chance of Snow",
        '130' => "Cloudy Heavy.\nShowers",
        '131' => "Cloudy chance of Sleet",
        '132' => "Cloudy chance of Heavy Snow",
        '140' => "Cloudy Rain,\nchance of thunderstorm",
        '141' => "Cloudy Sleet,\nchance of thunderstorm",
        '142' => "Cloudy, Heavy Snow,\nchance of thunderstorm",
        '200' => "Partly cloudy",
        '210' => "Partly cloudy,\nLight Showers",
        '211' => "Partly cloudy,\nSleet",
        '212' => "Partly cloudy,\nLight Snow",
        '220' => "Partly cloudy,\nRain",
        '221' => "Partly cloudy,\nSnow Showers",
        '222' => "Partly cloudy,\nSnow",
        '230' => "Partly cloudy,\nHeavy Showers",
        '231' => "Partly cloudy,\nSleet",
        '232' => "Partly cloudy,\nHeavy Snow",
        '240' => "Partly cloudy,\nRain, chance of thunderstorm",
        '241' => "Partly cloudy,\nchance of Sleet, thunderstorm",
        '242' => "Partly cloudy,\nHeavy Snow,chance of thunderstorm",
        '300' => "Mostly cloudy",
        '310' => "Mostly cloudy,\nLight Showers",
        '311' => "Mostly cloudy,\nSleet",
        '312' => "Mostly cloudy,\nLight Snow",
        '320' => "Mostly cloudy,\nRain",
        '321' => "Mostly cloudy,\nSnow Showers",
        '322' => "Mostly cloudy,\nSnow",
        '330' => "Mostly cloudy,\nHeavy Showers",
        '331' => "Mostly cloudy,\nSleet",
        '332' => "Mostly cloudy,\nHeavy Snow",
        '340' => "Mostly cloudy,\nRain, thunderstorm",
        '341' => "Mostly cloudy,\nSleet, thunderstorm",
        '342' => "Mostly cloudy,\nHeavy Snow, thunderstorm",
        '400' => "Overcast",
        '410' => "Overcast,\nLight Showers",
        '411' => "Overcast,\nSleet",
        '412' => "Overcast,\nLight Snow",
        '420' => "Overcast,\nRain",
        '421' => "Overcast,\nSnow Showers",
        '422' => "Overcast,\nSnow",
        '430' => "Overcast,\nHeavy Showers",
        '431' => "Overcast,\nSleet",
        '432' => "Overcast,\nHeavy Snow",
        '440' => "Overcast,\nRain, thunderstorm",
        '441' => "Overcast,\nSleet",
        '442' => "Overcast\nHeavy Snow, thunderstorm",
        '500' => "High clouds",
        '600' => "Fog",
    ];

    /** @var array<string, int|string>|null */
    private ?array $uaToCode = null;

    public function getCodeByUkrainianDescription(string $description): ?string
    {
        if ($this->uaToCode === null) {
            $this->uaToCode = [];
            foreach (self::CODE_TO_UA as $code => $uaDescription) {
                $this->uaToCode[$uaDescription] = $code;
            }
        }

        if (!isset($this->uaToCode[$description])) {
            return null;
        }

        return (string) $this->uaToCode[$description];
    }

    public function getDescriptionByCode(?string $code, Language $language): ?string
    {
        if ($code === null) {
            return null;
        }

        return match ($language) {
            Language::UA => self::CODE_TO_UA[$code] ?? null,
            Language::EN => self::CODE_TO_EN[$code] ?? null,
        };
    }
}
