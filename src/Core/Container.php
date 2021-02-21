<?php


namespace App\Core;


use App\Controller\BaseController;
use App\Database\TableQuery;
use App\Model\Token;
use App\Repository\BaseRepository;
use App\Repository\CallableRepository;
use Doctrine\ORM\EntityManager;
use Envms\FluentPDO\Query;
use Kreait\Firebase;
use Monolog\Logger;

/**
 * Class Container
 * @package App\Core
 *
 * @property-read Query db
 * @property-read Firebase firebase
 * @property-read callable repository
 * @property-read Token token
 * @property-read string uid Firebase UID
 * @property-read Logger logger
 * @property-read string publicKey
 * @property-read TableQuery tableQuery
 * @property-read EntityManager entityManager
 *
 */
class Container extends \Slim\Container
{
    /**
     * @param $repositoryClass
     * @return BaseRepository
     */
    public function getRepository($repositoryClass): BaseRepository
    {
        return ($this->repository)($repositoryClass);
    }

    /**
     * @param $controllerClass
     * @return BaseController
     */
    public function getController($controllerClass): BaseController
    {
        return ($this->controller)($controllerClass);
    }

    public function getRepositoryLazy($repositoryClass)
    {
        return new CallableRepository($this, $repositoryClass);
    }


}
//
//$container = new Container();
//$articleRepository = $container->getRepositoryLazy(ArticleRepository::class);
//$article = $articleRepository()->getArticlesByIds(18640);