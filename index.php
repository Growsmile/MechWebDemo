<?php
session_start();


error_reporting(E_ALL);
ini_set('display_errors', true);

define('ROOT', $_SERVER['DOCUMENT_ROOT'] . '/');

require ROOT . 'vendor/autoload.php';
$routes = include ROOT . 'config/routes.php';


require_once 'app/Email.php';
$email = new Email();
// var_dump($email);

require_once 'app/DataBase.php';
//$db = ConnectDb::getInstance();
 //var_dump($db);

function db() {
	return (ConnectDb::getInstance()) -> getConnection();
}


$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) use ($routes) {
	foreach ($routes as $url => $controller) {
		$r -> addRoute($controller[1], $url, $controller[0]);
	}
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
	$uri = substr($uri, 0, $pos);
}

$uri = rawurldecode($uri);

$routeInfo = $dispatcher -> dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
	case FastRoute\Dispatcher::NOT_FOUND:
		$controller = 'error';
		$action = 'notfound';
		
		echo '404!';
		break;
	case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
		$allowedMethods = $routeInfo[1];
		echo '405!';
		break;
	case FastRoute\Dispatcher::FOUND:
		$handler = $routeInfo[1];
		$vars = $routeInfo[2];
		
		list($controller, $action) = explode('@', $handler);

		require 'controllers/' . $controller . '.php';

		$class = ucfirst($controller) . 'Controller';
		$action = $action.'Action';
		
		$contr = new $class();
		echo $contr -> $action($vars);

		break;
}

// if (isset($_SESSION['user'])) {
//     header('Location: /login');
// }