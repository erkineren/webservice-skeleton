<?php


namespace App\Repository;


use App\Core\Container;

final class CallableRepository
{
    /**
     * @var Container
     */
    private $container;
    /**
     * @var string
     */
    private $repositoryClass;


    /**
     * CallableRepository constructor.
     * @param Container $container
     * @param string $repositoryClass
     */
    public function __construct(Container $container, string $repositoryClass)
    {
        $this->container = $container;
        $this->repositoryClass = $repositoryClass;

    }

    /**
     * @return BaseRepository
     */
    public function __invoke()
    {
        return $this->container->getRepository($this->repositoryClass);
    }
}