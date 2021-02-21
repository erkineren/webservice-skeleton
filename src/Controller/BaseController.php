<?php


namespace App\Controller;

use App\Response\ApiResponse;
use Slim\Container;
use Slim\Http\Request;

abstract class BaseController
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ApiResponse
     */
    protected $response;

    public function __construct(Container $container)
    {
        $this->container = $container;

    }

    /**
     * @param Request $request
     * @return BaseController
     */
    public function setRequest(Request $request): BaseController
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @param ApiResponse $response
     * @return BaseController
     */
    public function setResponse(ApiResponse $response): BaseController
    {
        $this->response = $response;
        return $this;
    }


}