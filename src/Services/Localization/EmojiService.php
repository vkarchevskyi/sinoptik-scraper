<?php

declare(strict_types=1);

namespace Vkarchevskyi\SinoptikUaParser\Services\Localization;

final class EmojiService
{
    /** @var array<string, string> */
    private array $data;

    public function __construct()
    {
        /** @var array<string, string> $data */
        $data = require __DIR__ . '/../../../lang/emoji.php';
        $this->data = $data;
    }

    public function get(string $code): ?string
    {
        return $this->data[$code] ?? null;
    }
}
