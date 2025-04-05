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

        if ($status !== 200) {
            throw new RuntimeException('Status is not successful. Current status: ' . $status);
        }

        $html = gzdecode($html);
        if (false === $html) {
            throw new RuntimeException("Provided content is not a gzip encoded string");
        }

        return $html;
    }

    protected function getFullUrl(string $city, string $date): string
    {
        return sprintf("%s/%s/%s", self::BASE_URL, $city, $date);
    }
}