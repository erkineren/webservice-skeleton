<?php


namespace App\Controller;

use App\Annotations\Route;
use App\Annotations\RouteGroup;
use Slim\Http\Response;


/**
 * Class Controller
 * @package App\Controller
 *
 * @RouteGroup("/user")
 */
class Controller extends BaseController
{

    protected $users = [
        1 => ['id' => 1, 'name' => 'John'],
        2 => ['id' => 2, 'name' => 'Doe'],
    ];

    /**
     * @Route("/{id:\d+}")
     *
     * @param $id
     * @return Response
     */
    function getUser($id): Response
    {
        if ($user = $this->users[$id] ?? false) {
            return $this->response->withJsonAuto($this->users[$id]);
        }
        return $this->response
            ->withStatus(404)
            ->withJsonError('Not found');
    }

    /**
     * @Route("[/]")
     *
     * @return Response
     */
    function getUsers(): Response
    {
        return $this->response->withJsonAuto($this->users);
    }
}