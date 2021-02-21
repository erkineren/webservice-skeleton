<?php


namespace App\Core;


use App\Annotations\Route;
use App\Annotations\RouteGroup;
use App\Response\ApiResponse as Response;
use Doctrine\Common\Annotations\AnnotationReader;
use Envms\FluentPDO\Query;
use Kreait\Firebase;
use ReflectionClass;
use ReflectionMethod;
use Slim\Http\Request;


/**
 * Class App
 * @package App\Core
 *
 * @method Container getContainer
 * @property-read Query db
 * @property-read Firebase $firebase
 *
 */
class App extends \Slim\App
{

    public function registerRoutes(bool $registerControllers = true)
    {
        $app =& $this;
        foreach (glob(BASE_PATH . '/config/routes/*.php') as $routefile) {
            $routes = require_once $routefile;
            $routes($this);
        }
        if ($registerControllers) {
            foreach (glob(BASE_PATH . '/src/Controller/*.php') as $controllerFile) {
                $classSimpleName = basename($controllerFile, '.php');
                $classFullName = "App\\Controller\\$classSimpleName";


                $reflectionClass = new ReflectionClass($classFullName);
                $reader = new AnnotationReader();
                $routeGroup = $reader->getClassAnnotation($reflectionClass, RouteGroup::class);
                if (!$routeGroup) continue;
                $routeGroupPattern = $routeGroup->pattern;

                foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                    $route = $reader->getMethodAnnotation($method, Route::class);
                    if (!$route) continue;
                    if ((strpos($method->getName(), 'get')) === 0) $requestMethod = 'GET';
                    elseif ((strpos($method->getName(), 'post')) === 0) $requestMethod = 'POST';
                    elseif ((strpos($method->getName(), 'put')) === 0) $requestMethod = 'PUT';
                    elseif ((strpos($method->getName(), 'delete')) === 0) $requestMethod = 'DELETE';
                    else continue;

                    $this->map([$requestMethod], $routeGroupPattern . $route->pattern, function (Request $request, Response $response, $arguments) use ($classFullName, $app, $method) {

                        $controllerObject = $app->getContainer()->getController($classFullName);
                        $controllerObject->setRequest($request)->setResponse($response);

                        $result = $method->invokeArgs($controllerObject, $arguments);
                        return $result;
                    });
                }
            }
        }
    }
}