<?php

declare(strict_types=1);

namespace Vkarchevskyi\SinoptikUaParser;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Dom\Element;
use Dom\HTMLDocument;
use Exception;
use LogicException;
use RuntimeException;

class HtmlParser
{
    protected const string BASE_URL = 'https://sinoptik.ua/pohoda';
    protected const string DATE_FORMAT = 'Y-m-d';
    protected const string TIMEZONE = 'Europe/Kyiv';

    protected string $city;
    protected DateTimeInterface $date;

    public function __construct(string $city, DateTime|string|null $date = null)
    {
        $this->setCity($city);

        if (!empty($date)) {
            $this->setDate($date);
        }
    }

    /**
     * @return list<array{time: string, data: array<string, string>}>
     *
     * @throws Exception
     * @throws LogicException
     */
    public function getData(bool $onlyCurrentTime = true): array
    {
        $data = [];
        $dom = $this->getHtmlDocumentObjectModel($this->getFullUrl());
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
                $propertyName = $this->getNameByTableIndex($weatherDataIndex);
                $propertyValue = $this->parsePropertyValueByTableIndex($weatherDataIndex, $weatherDataItem);

                $data[$timeIndex]['data'][$propertyName] = $propertyValue;
            }
        }

        return $onlyCurrentTime ? $data[$this->getCurrentTimeIndex($data)] : $data;
    }

    public function setCity(string $value): void
    {
        if (empty($value)) {
            throw new LogicException('The city must not be empty');
        }

        $this->city = mb_strtolower($value);
    }

    public function setDate(DateTimeInterface|string $date): void
    {
        if (is_string($date)) {
            if (!$newDate = DateTimeImmutable::createFromFormat(self::DATE_FORMAT, $date)) {
                throw new LogicException('Incorrect date format. The format must be "' . self::DATE_FORMAT . '".');
            }

            $this->date = $newDate;
        } elseif ($date instanceof DateTimeInterface) {
            $this->date = $date;
        }

        $this->date->setTimezone(new DateTimeZone(self::TIMEZONE));
    }

    /**
     * @param list<array{time: string, data: array<string, string>}> $data
     * @return int
     */
    protected function getCurrentTimeIndex(array $data): int
    {
        $intervals = [];

        foreach ($data as $dataPerTime) {
            [$hours, $minutes] = explode(':', $dataPerTime['time']);

            $dateTimeFromTime = new DateTime()
                ->setTimezone(new DateTimeZone('Europe/Kyiv'))
                ->setTime((int)$hours, (int)$minutes);

            $intervals[] = abs($this->date->getTimestamp() - $dateTimeFromTime->getTimestamp());
        }

        asort($intervals);

        return key($intervals);
    }

    /**
     * @throws Exception
     */
    protected function getHtmlDocumentObjectModel(string $url): HTMLDocument
    {
        $c = curl_init($url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

        $html = curl_exec($c);

        if (curl_error($c)) {
            throw new RuntimeException(curl_error($c));
        }

        $status = curl_getinfo($c, CURLINFO_HTTP_CODE);

        curl_close($c);

        if ($status !== 200) {
            throw new RuntimeException('Status is not successful');
        }

        var_dump($html);

        return HTMLDocument::createFromString(gzdecode($html), LIBXML_NOERROR);
    }

    protected function getFullUrl(): string
    {
        return sprintf(
            "%s/%s/%s",
            self::BASE_URL,
            $this->city,
            $this->date->format(self::DATE_FORMAT)
        );
    }

    protected function getNameByTableIndex(int $index): string
    {
        return match ($index) {
            0 => 'Picture',
            1 => 'Temperature',
            2 => 'Feels like',
            3 => 'Pressure',
            4 => 'Humidity',
            5 => 'Wind (m/s)',
            6 => 'Probability of Precipitation',
        };
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
