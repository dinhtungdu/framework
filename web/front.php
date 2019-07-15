<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing;
use Symfony\Component\HttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;

$request = Request::createFromGlobals();
$routes  = include __DIR__ . '/../src/app.php';

$context = new Routing\RequestContext();
$matcher = new Routing\Matcher\UrlMatcher($routes, $context);

$dispatcher = new EventDispatcher();
$dispatcher->addListener('response', [new Simplex\GoogleListener(), 'onResponse']);
$dispatcher->addListener('response', [new Simplex\ContentLengthListener(), 'onResponse'], -255);

$controllerResolver = new HttpKernel\Controller\ControllerResolver();
$argumentResolver   = new HttpKernel\Controller\ArgumentResolver();

$framework = new Simplex\Framework($dispatcher, $matcher, $controllerResolver, $argumentResolver);
$response  = $framework->handle($request);

$response->send();
