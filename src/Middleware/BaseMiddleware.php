<?php


namespace App\Middleware;


use App\Core\Container;

abstract class BaseMiddleware
{
    /**
     * @var Container
     */
    protected $container;


    /**
     * CheckCustomerGroup constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
}