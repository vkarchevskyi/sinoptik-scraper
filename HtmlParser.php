<?php

declare(strict_types=1);

use Dom\Element;
use Dom\HTMLDocument;

class HtmlParser
{
    private const string BASE_URL = 'https://sinoptik.ua';
    private const string DATE_FORMAT = 'Y-m-d';

    public string $city {
        set {
            if (empty($value)) {
                throw new LogicException('The city must not be empty');
            }

            $this->city = mb_strtolower($value);
        }
    }

    public DateTime $date {
        set(DateTime|string $date) {
            if (is_string($date)) {
                if (!$newDate = DateTime::createFromFormat(self::DATE_FORMAT, $date)) {
                    throw new LogicException('Incorrect date format. The format must be "' . self::DATE_FORMAT . '".');
                }

                $this->date = $newDate;
            } elseif ($date instanceof DateTime) {
                $this->date = $date;
            }
        }
    }

    public function __construct(string $city = 'Киев', DateTime|string|null $date = null)
    {
        $this->city = $city;

        if (!empty($date)) {
            $this->date = $date;
        }
    }

    /**
     * @return array{time: string, data: array<string, string>}
     *
     * @throws Exception
     * @throws LogicException
     */
    public function getData(): array
    {
        $dom = $this->getHtmlDocumentObjectModel($this->getFullUrl());

        $data = [];
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

        return $data;
    }

    /**
     * @throws Exception
     */
    private function getHtmlDocumentObjectModel(string $url): HTMLDocument
    {
        $c = curl_init($url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

        $html = curl_exec($c);

        if (curl_error($c)) {
            throw new Exception(curl_error($c));
        }

        $status = curl_getinfo($c, CURLINFO_HTTP_CODE);

        curl_close($c);

        if ($status !== 200) {
            throw new LogicException('Status is not successful');
        }

        return Dom\HTMLDocument::createFromString($html, LIBXML_NOERROR);
    }

    private function getFullUrl(): string
    {
        return sprintf(
            "%s/погода-%s/%s",
            self::BASE_URL,
            $this->city,
            $this->date->format(self::DATE_FORMAT)
        );
    }

    private function getNameByTableIndex(int $index): string
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

    private function parsePropertyValueByTableIndex(int $index, Element $node): string
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
