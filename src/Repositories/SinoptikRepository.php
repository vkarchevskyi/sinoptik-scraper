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
        curl_setopt($c, CURLOPT_HEADER, 1);

        $response = curl_exec($c);
        if (curl_error($c)) {
            throw new RuntimeException(curl_error($c));
        }

        $status = curl_getinfo($c, CURLINFO_HTTP_CODE);
        if ($status !== 200 || is_bool($response)) {
            throw new RuntimeException('Status is not successful. Current status: ' . $status);
        }

        $headerSize = curl_getinfo($c, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $headerSize);
        $html = substr($response, $headerSize);

        curl_close($c);

        if (stripos($headers, 'Content-Encoding: gzip') === false) {
            return $html;
        }

        $html = gzdecode($html);
        if ($html === false) {
            throw new RuntimeException('Provided content is not a gzip encoded string');
        }

        return $html;
    }

    protected function getFullUrl(string $city, string $date): string
    {
        return sprintf('%s/%s/%s', self::BASE_URL, $city, $date);
    }
}
