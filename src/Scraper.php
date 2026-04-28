<?php

declare(strict_types=1);

namespace Vkarchevskyi\SinoptikUaParser;

use DateTimeImmutable;
use Dom\Element;
use Dom\HTMLDocument;
use Exception;
use LogicException;
use RuntimeException;
use Vkarchevskyi\SinoptikUaParser\DataTransferObjects\WeatherData;
use Vkarchevskyi\SinoptikUaParser\DataTransferObjects\WeatherPeriodData;
use Vkarchevskyi\SinoptikUaParser\Enums\Language;
use Vkarchevskyi\SinoptikUaParser\Enums\WeatherProperty;
use Vkarchevskyi\SinoptikUaParser\Repositories\SinoptikRepository;
use Vkarchevskyi\SinoptikUaParser\Services\CurrentTimeIndexService;
use Vkarchevskyi\SinoptikUaParser\Services\Localization\WeatherTranslationService;

readonly class Scraper
{
    protected SinoptikRepository $repository;
    protected CurrentTimeIndexService $currentTimeIndexService;
    protected WeatherTranslationService $weatherTranslationService;

    public function __construct(
        protected string $city,
        protected DateTimeImmutable $date,
        protected string $dateFormat,
        protected Language $language,
    ) {
        $this->repository = new SinoptikRepository();
        $this->currentTimeIndexService = new CurrentTimeIndexService();
        $this->weatherTranslationService = new WeatherTranslationService();
    }

    /**
     * @throws Exception
     * @throws LogicException
     * @throws RuntimeException
     */
    public function getCurrentTimeData(): WeatherPeriodData
    {
        $data = $this->getData();

        return $data[$this->currentTimeIndexService->get($data, $this->date)];
    }

    /**
     * @return array<int, WeatherPeriodData>
     *
     * @throws Exception
     * @throws LogicException
     * @throws RuntimeException
     */
    public function getData(): array
    {
        $result = [];

        $dom = $this->getHtmlDocumentObjectModel();
        $timeNodes = $dom->querySelectorAll('table > thead > tr:last-child > td');
        $weatherNodes = $dom->querySelectorAll('table > tbody > tr');

        /** @var Element $timeNode */
        foreach ($timeNodes as $timeNode) {
            $weatherData = new WeatherData(
                description: '',
                temperature: '',
                feelsLike: '',
                pressure: '',
                humidity: '',
                wind: '',
                precipitationProbability: '',
            );
            $result[] = new WeatherPeriodData($timeNode->innerHTML, $weatherData);
        }

        /** @var Element $weatherNode */
        foreach ($weatherNodes as $weatherDataIndex => $weatherNode) {
            /** @var Element $weatherDataItem */
            foreach ($weatherNode->childNodes as $timeIndex => $weatherDataItem) {
                $property = WeatherProperty::from($weatherDataIndex);

                if ($property === WeatherProperty::Description) {
                    $description = $this->parsePropertyValueByTableIndex($property, $weatherDataItem);
                    $code = $this->weatherTranslationService->getCodeByUkrainianDescription($description);
                    $description = $this->weatherTranslationService->getDescriptionByCode($code, $this->language)
                        ?? $description;

                    $previousResult = $result[$timeIndex];
                    $result[$timeIndex] = new WeatherPeriodData(
                        $previousResult->time,
                        new WeatherData(
                            description: $description,
                            temperature: $previousResult->data->temperature,
                            feelsLike: $previousResult->data->feelsLike,
                            pressure: $previousResult->data->pressure,
                            humidity: $previousResult->data->humidity,
                            wind: $previousResult->data->wind,
                            precipitationProbability: $previousResult->data->precipitationProbability,
                            code: $code,
                        ),
                    );
                } else {
                    $value = $this->parsePropertyValueByTableIndex($property, $weatherDataItem);
                    $result[$timeIndex] = $this->updateWeatherData($result[$timeIndex], $property, $value);
                }
            }
        }

        return $result;
    }

    protected function updateWeatherData(WeatherPeriodData $periodData, WeatherProperty $property, string $value): WeatherPeriodData
    {
        $data = $periodData->data;

        return new WeatherPeriodData(
            $periodData->time,
            new WeatherData(
                description: $property === WeatherProperty::Description ? $value : $data->description,
                temperature: $property === WeatherProperty::Temperature ? $value : $data->temperature,
                feelsLike: $property === WeatherProperty::FeelsLike ? $value : $data->feelsLike,
                pressure: $property === WeatherProperty::Pressure ? $value : $data->pressure,
                humidity: $property === WeatherProperty::Humidity ? $value : $data->humidity,
                wind: $property === WeatherProperty::Wind ? $value : $data->wind,
                precipitationProbability: $property === WeatherProperty::PrecipitationProbability ? $value : $data->precipitationProbability,
                code: $data->code,
            ),
        );
    }

    /**
     * @throws Exception
     */
    protected function getHtmlDocumentObjectModel(): HTMLDocument
    {
        $html = $this->repository->getHtml($this->city, $this->date->format($this->dateFormat));

        return HTMLDocument::createFromString($html);
    }

    protected function parsePropertyValueByTableIndex(WeatherProperty $property, Element $node): string
    {
        $value = match ($property) {
            WeatherProperty::Description => $this->getAriaLabel($node),
            WeatherProperty::Wind => $node->textContent,
            default => $node->innerHTML
        };

        if (is_null($value)) {
            throw new RuntimeException("Node with id $node->id must contain textContent");
        }

        return match ($property) {
            WeatherProperty::Temperature, WeatherProperty::FeelsLike => mb_substr($value, 0, mb_strlen($value) - 1), // Remove degree sign
            WeatherProperty::PrecipitationProbability => $value === '-' ? '0' : $value, // Replace '-' sign with 0 (0% probability of precipitation)
            default => $value,
        };
    }

    protected function getAriaLabel(Element $node): string
    {
        $element = $node->querySelector('div[aria-label]');
        if ($element === null) {
            throw new RuntimeException('Weather description element not found');
        }

        $attribute = $element->getAttributeNode('aria-label');
        if ($attribute === null) {
            throw new RuntimeException('aria-label attribute not found');
        }

        $value = $attribute->textContent;
        if ($value === null) {
            throw new RuntimeException('aria-label textContent is null');
        }

        return $value;
    }

}
