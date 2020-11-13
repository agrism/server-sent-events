<?php

require __DIR__.'/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

function env(string $key, $default = null)
{
	return $_ENV[$key] ?? $default;
}

function view($path)
{
	include __DIR__.'/src/views/'.$path.'.php';
}

$routeSegments = $_SERVER['REQUEST_URI'] ?? '';
$routeSegments = explode('/', $routeSegments);
$routeSegments = array_filter($routeSegments);
$routePath = implode('/', $routeSegments);

switch ($routePath) {
	case 'migrate':
		return \App\Migrations\Migrate::factory()->index();
	case 'server/add-event':
		return \App\Controllers\ServerController::factory()->addEvent();
	case 'server/stream':
		return \App\Controllers\ServerController::factory()->stream();
	default:
		return \App\Controllers\FrontendController::factory()->index();
}

