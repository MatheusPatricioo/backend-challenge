<?php

use Contatoseguro\TesteBackend\Middleware\ExceptionMiddleware;
use Contatoseguro\TesteBackend\Middleware\JsonResponseMiddleware;
use Slim\App;

/** @var App $app*/
$app->addBodyParsingMiddleware();
$app->add(ExceptionMiddleware::class);
$app->add(JsonResponseMiddleware::class);
