<?php

declare(strict_types=1);

require_once 'HtmlParser.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$city = $_REQUEST['city'] ?? 'Киев';
$date = $_REQUEST['date'] ?? new DateTime();
$onlyCurrentTime = !empty($_REQUEST['current-time']);

$data = new HtmlParser($city, $date)->getData($onlyCurrentTime);

echo json_encode($data);
