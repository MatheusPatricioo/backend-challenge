<?php

use Slim\Factory\AppFactory;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

$app = AppFactory::create();

require dirname(__DIR__, 2) . '/src/Config/middlewares.php';
require dirname(__DIR__, 2) . '/src/Config/routes.php';

return $app;
