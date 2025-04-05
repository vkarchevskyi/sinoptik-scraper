<?php

declare(strict_types=1);

namespace Vkarchevskyi\SinoptikUaParser;

use DateInvalidTimeZoneException;
use DateTimeImmutable;
use DateTimeZone;
use LogicException;

class ScraperFactory
{
    protected const string DATE_FORMAT = 'Y-m-d';

    protected string $city = 'Kyiv';
    protected string $timezone = 'Europe/Kyiv';
    protected DateTimeImmutable $date;

    /**
     * @throws DateInvalidTimeZoneException
     */
    public function make(): Scraper
    {
        if (!isset($this->date)) {
            $this->date = new DateTimeImmutable();
        }

        return new Scraper(
            $this->city,
            $this->date->setTimezone(new DateTimeZone($this->timezone)),
            self::DATE_FORMAT
        );
    }

    public function setCity(string $value): self
    {
        if (empty($value)) {
            throw new LogicException('The city must not be empty');
        }

        $this->city = mb_strtolower($value);

        return $this;
    }

    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function setDate(DateTimeImmutable|string $date): self
    {
        if ($date instanceof DateTimeImmutable) {
            $this->date = $date;

            return $this;
        }

        if (!$newDate = DateTimeImmutable::createFromFormat(self::DATE_FORMAT, $date)) {
            throw new LogicException('Incorrect date format. The format must be "' . self::DATE_FORMAT . '".');
        }
        $this->date = $newDate;

        return $this;
    }
}
