<?php

declare(strict_types=1);

namespace Vkarchevskyi\SinoptikUaParser;

use DateTimeImmutable;
use DateTimeZone;
use Dom\Element;
use Dom\HTMLDocument;
use Exception;
use LogicException;
use RuntimeException;
use Vkarchevskyi\SinoptikUaParser\DataTransferObjects\WeatherData;

class HtmlParser
{
    protected const string DATE_FORMAT = 'Y-m-d';
    protected const string TIMEZONE = 'Europe/Kyiv';

    protected private(set) Repositories\SinoptikRepository $repository;
    protected private(set) Services\NameByTableIndexService $nameByTableIndexService;
    protected private(set) Services\CurrentTimeIndexService $currentTimeIndexService;

    protected private(set) string $city;
    protected private(set) DateTimeImmutable $date;

    public function __construct(string $city, DateTimeImmutable|string|null $date = null)
    {
        $this->setCity($city);

        if (!empty($date)) {
            $this->setDate($date);
        }

        $this->repository = new Repositories\SinoptikRepository();
        $this->nameByTableIndexService = new Services\NameByTableIndexService();
        $this->currentTimeIndexService = new Services\CurrentTimeIndexService();
    }

    /**
     * @throws Exception
     * @throws LogicException
     * @throws RuntimeException
     */
    public function getCurrentTimeData(): WeatherData
    {
        $data = $this->getData();

        return $data[$this->currentTimeIndexService->get($data, $this->date)];
    }

    /**
     * @return list<WeatherData>
     *
     * @throws Exception
     * @throws LogicException
     * @throws RuntimeException
     */
    public function getData(): array
    {
        $data = [];

        $dom = $this->getHtmlDocumentObjectModel();
        $timeNodes = $dom->querySelectorAll('table > thead > tr:last-child > td');

        /** @var Element $timeNode */
        foreach ($timeNodes as $timeNode) {
            $data[] = ['time' => $timeNode->innerHTML, 'data' => []];
        }

        $weatherNodes = $dom->querySelectorAll('table > tbody > tr');

        /** @var Element $weatherNode */
        foreach ($weatherNodes as $weatherDataIndex => $weatherNode) {
            // Skip pictures of weather
            if ($weatherDataIndex === 0) {
                continue;
            }

            /** @var Element $weatherDataItem */
            foreach ($weatherNode->childNodes as $timeIndex => $weatherDataItem) {
                $propertyName = $this->nameByTableIndexService->get($weatherDataIndex);
                $propertyValue = $this->parsePropertyValueByTableIndex($weatherDataIndex, $weatherDataItem);

                $data[$timeIndex]['data'][$propertyName] = $propertyValue;
            }
        }

        return array_map(static fn (array $item): WeatherData => new WeatherData($item['time'], $item['data']), $data);
    }

    public function setCity(string $value): void
    {
        if (empty($value)) {
            throw new LogicException('The city must not be empty');
        }

        $this->city = mb_strtolower($value);
    }

    public function setDate(DateTimeImmutable|string $date): void
    {
        if (is_string($date)) {
            if (!$newDate = DateTimeImmutable::createFromFormat(self::DATE_FORMAT, $date)) {
                throw new LogicException('Incorrect date format. The format must be "' . self::DATE_FORMAT . '".');
            }

            $this->date = $newDate;
        } elseif ($date instanceof DateTimeImmutable) {
            $this->date = $date;
        }

        $this->date->setTimezone(new DateTimeZone(self::TIMEZONE));
    }

    /**
     * @throws Exception
     */
    protected function getHtmlDocumentObjectModel(): HTMLDocument
    {
        $html = $this->repository->getHtml($this->city, $this->date->format(self::DATE_FORMAT));

        return HTMLDocument::createFromString($html, LIBXML_NOERROR);
    }

    protected function parsePropertyValueByTableIndex(int $index, Element $node): string
    {
        $value = $index === 5 ? $node->textContent : $node->innerHTML;

        return match ($index) {
            0 => '', // Remove data about weather picture
            1, 2 => mb_substr($value, 0, mb_strlen($value) - 1), // Remove degree sign
            6 => $value === '-' ? '0' : $value, // Replace '-' sign with 0 (0% probability of precipitation)
            default => $value,
        };
    }
}
