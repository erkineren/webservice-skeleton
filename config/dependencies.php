<?php


use App\Core\App;
use App\Core\Container;
use App\Database\TableQuery;
use App\Exception\NotFoundException;
use App\Model\Token;
use App\Response\ApiResponse as Response;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Envms\FluentPDO\Query;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (App $app) {

    $container = $app->getContainer();

    $container["token"] = function (Container $c) {
        return new Token;
    };

    $container['response'] = function (Container $c) {
        return new Response();
    };

    $container['logger'] = function (Container $c) {
        $settings = $c->settings['logger'];
        $logger = new Logger($settings['name']);
        $logger->pushProcessor(new UidProcessor());
        $logger->pushHandler(new StreamHandler($settings['path'], $settings['level']));
        return $logger;
    };

    $container['errorHandler'] = function (Container $c) {
        return function (Request $request, Response $response, Exception $exception) use ($c) {
            return $response->withJsonError($exception->getMessage(), 500);
        };
    };

    $container['notFoundHandler'] = function (Container $c) {
        return function (Request $request, Response $response) {
            return $response->withJsonError('Endpoint Not Found !', 404);
        };
    };

    $container['db'] = function (Container $c) {
        $pdo = new PDO('mysql:host=' . getenv("DB_HOST") . ';dbname=' . getenv("DB_NAME"), getenv("DB_USER"), getenv("DB_PASSWORD"));
        $pdo->exec("SET NAMES 'utf8'; SET CHARSET 'utf8'");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return new Query($pdo);
    };

    $container['repository'] = function (Container $c) {
        return function ($repositoryClass) use ($c) {
            if (class_exists($repositoryClass)) {
                if (!$c->has($repositoryClass)) {
                    $c->offsetSet($repositoryClass, new $repositoryClass($c));
                }
                return $c->get($repositoryClass);
            }

            throw new NotFoundException("$repositoryClass is not exists");
        };
    };

    $container['controller'] = function (Container $c) {
        return function ($controllerClass) use ($c) {
            if (class_exists($controllerClass)) {
                if (!$c->has($controllerClass)) {
                    $c->offsetSet($controllerClass, new $controllerClass($c));
                }
                return $c->get($controllerClass);
            }

            throw new NotFoundException("$controllerClass is not exists");
        };
    };

    $container['tableQuery'] = function (Container $c) {
        return new TableQuery($c->db);
    };

    $container['entityManager'] = function (Container $c) {
        $paths = array(realpath(BASE_PATH . '/src/Database/Entity'));
        $isDevMode = true;


        $dbParams = array(
            'driver' => 'pdo_mysql',
            'user' => getenv("DB_USER"),
            'password' => getenv("DB_PASSWORD"),
            'dbname' => getenv("DB_NAME"),
            'server' => 'localhost'
        );

        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, null, null, false);
        return EntityManager::create($dbParams, $config);
    };

};
