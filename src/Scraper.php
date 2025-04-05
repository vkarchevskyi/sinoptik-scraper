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

readonly class Scraper
{
    protected Repositories\SinoptikRepository $repository;
    protected Services\CurrentTimeIndexService $currentTimeIndexService;

    public function __construct(
        protected string $city,
        protected DateTimeImmutable $date,
        protected string $dateFormat,
    ) {
        $this->repository = new Repositories\SinoptikRepository();
        $this->currentTimeIndexService = new Services\CurrentTimeIndexService();
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
            /** @var Element $weatherDataItem */
            foreach ($weatherNode->childNodes as $timeIndex => $weatherDataItem) {
                $value = $this->parsePropertyValueByTableIndex($weatherDataIndex, $weatherDataItem);

                $data[$timeIndex]['data'][$weatherDataIndex] = $value;
            }
        }

        return array_map(
            static fn (array $item): WeatherPeriodData => new WeatherPeriodData(
                $item['time'],
                new WeatherData(...$item['data']),
            ),
            $data
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

    protected function parsePropertyValueByTableIndex(int $index, Element $node): string
    {
        $value = match ($index) {
            0 => $node->querySelector('div[aria-label]')->getAttributeNode('aria-label')->textContent,
            5 => $node->textContent,
            default => $node->innerHTML
        };

        if (is_null($value)) {
            throw new RuntimeException("Node with id $node->id must contain textContent");
        }

        return match ($index) {
            1, 2 => mb_substr($value, 0, mb_strlen($value) - 1), // Remove degree sign
            6 => $value === '-' ? '0' : $value, // Replace '-' sign with 0 (0% probability of precipitation)
            default => $value,
        };
    }
}
