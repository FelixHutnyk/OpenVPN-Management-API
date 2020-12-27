<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use Awurth\SlimValidation\Validator;
use Respect\Validation\Validator as V;
use CM\InterfaceManagement\ConnectionManager;

$container = $app->getContainer();

$container['cache'] = function () {
	return new \Slim\HttpCache\CacheProvider();
};

$container['validator'] = function () {
	return new Awurth\SlimValidation\Validator();
};

$container["logger"] = function ($c) {
	$log = new Logger("api");
	$log->pushHandler(new StreamHandler(__DIR__ . "/logs/app.log", Logger::INFO));

	return $log;
};

$app->add(new \Slim\HttpCache\Cache('private', 300, true));

$app->add(function (Request $request, Response $response, $next) {
	$response = $next($request, $response);
	$response = $response->withHeader('Access-Control-Allow-Origin', '*')
	->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
	->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
	return $response;
});

$app->group("/v1", function () use ($app) {
	$app->get("/ping", function (Request $request, Response $response) {
		return "pong";
	});

	$app->get("/connections", function (Request $request, Response $response) {

		$serverManager = new ConnectionManager('tcp://127.0.0.1:48205');
		$connections = $serverManager->connections();

		$data['connections'] = $connections;
		$response = $response->withHeader("Content-Type", "application/json")
		->withStatus(200, "OK")
		->withJson($data);
		return $response;
	});

	$app->post("/disconnect", function (Request $request, Response $response) {

		$this->validator->request($request, [
			'username' => V::alnum(),
		]);

		if (!$this->validator->isValid()) {
			return validationerror($response);
		}

		$username = $request->getParam("username");

		$serverManager = new ConnectionManager('tcp://127.0.0.1:48205');

		$status = $serverManager->disconnect($username);

		if($status) {
			return ok($response);
		}


		return error($response);
	});







});

?>
