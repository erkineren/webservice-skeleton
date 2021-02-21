<?php

use App\Core\App;
use App\Core\Container;
use Doctrine\Common\Annotations\AnnotationRegistry;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(1);

define('BASE_PATH', __DIR__);

require __DIR__ . '/vendor/autoload.php';

AnnotationRegistry::registerLoader('class_exists');

require __DIR__ . '/config/constants.php';

foreach (glob(__DIR__ . '/helpers/*.php') as $helperFile) {
    include_once $helperFile;
}

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

// Instantiate the app
$settings = require __DIR__ . '/config/settings.php';
$app = new App(new Container($settings));

// Set up dependencies
$dependencies = require __DIR__ . '/config/dependencies.php';
$dependencies($app);

// Register middleware
$middleware = require __DIR__ . '/config/middleware.php';
$middleware($app);

// Register routes
$app->registerRoutes(true);
