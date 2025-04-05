<?php

declare(strict_types=1);

namespace Vkarchevskyi\SinoptikUaParser\Services;

use LogicException;

class NameByTableIndexService
{
    /**
     * @throws LogicException
     */
    public function get(int $index): string
    {
        return match ($index) {
            0 => 'Picture',
            1 => 'Temperature',
            2 => 'Feels like',
            3 => 'Pressure',
            4 => 'Humidity',
            5 => 'Wind (m/s)',
            6 => 'Probability of Precipitation',
            default => throw new LogicException("$index is undefined table index"),
        };
    }
}
