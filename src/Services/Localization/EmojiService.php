<?php

declare(strict_types=1);

namespace Vkarchevskyi\SinoptikUaParser\Services\Localization;

final class EmojiService
{
    private array $data = [];

    public function __construct()
    {
        if (empty($this->data)) {
            $this->data = require __DIR__ . '/../../../lang/ua/weather.php';
        }
    }

    public function get(string $description): ?string
    {
        return $this->data[$description] ?? null;
    }
}