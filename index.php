<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Vkarchevskyi\SinoptikUaParser\HtmlParser;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$city = $_REQUEST['city'] ?? 'Kyiv';
$date = $_REQUEST['date'] ?? new DateTimeImmutable();
$onlyCurrentTime = !empty($_REQUEST['current-time']);

$data = $onlyCurrentTime
    ? new HtmlParser($city, $date)->getCurrentTimeData()
    : new HtmlParser($city, $date)->getData();

echo json_encode($data);
