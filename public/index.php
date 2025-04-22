<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Container\Container;
use App\Service\Cors\CORSService;
use App\Router\RouteDispatcher;
use App\Service\Request\Request;

header('Content-Type: text/html; charset=UTF-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$container = new Container();
$container->registerClassesInDirectory(__DIR__ . '/../src');

$container->get(CORSService::class)->setupHeaders();
$request = $container->get(Request::class);

$url = $request->getUrl();
$method = $request->getMethod(); 

$routeDispatcher = $container->get(RouteDispatcher::class);
$routeDispatcher->dispatch($url, $method);
