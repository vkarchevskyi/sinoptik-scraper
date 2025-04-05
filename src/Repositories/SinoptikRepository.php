<?php

declare(strict_types=1);

namespace Vkarchevskyi\SinoptikUaParser\Repositories;

use RuntimeException;

class SinoptikRepository
{
    protected const string BASE_URL = 'https://sinoptik.ua/pohoda';

    /**
     * @throws RuntimeException
     */
    public function getHtml(string $city, string $date): string
    {
        $url = $this->getFullUrl($city, $date);

        $c = curl_init($url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

        $html = curl_exec($c);

        if (curl_error($c)) {
            throw new RuntimeException(curl_error($c));
        }

        $status = curl_getinfo($c, CURLINFO_HTTP_CODE);

        curl_close($c);

        if ($status !== 200 || is_bool($html)) {
            throw new RuntimeException('Status is not successful. Current status: ' . $status);
        }

        $gzDecodedHtml = gzdecode($html);

        // For some reason sinoptik.ua returns Gzip encoded HTML for Kyiv and raw HTML for other cities
        if ($gzDecodedHtml === false) {
            return $html;
        }

        return $gzDecodedHtml;
    }

    protected function getFullUrl(string $city, string $date): string
    {
        return sprintf('%s/%s/%s', self::BASE_URL, $city, $date);
    }
}
