<?php


namespace App\Repository;


use App\Database\TableQuery;
use App\Model\Token;
use Envms\FluentPDO\Query;
use Kreait\Firebase;
use Monolog\Logger;
use PDO;
use Slim\Container;

/**
 * Class BaseRepository
 * @package App\Repository
 *
 * @property-read Query db
 * @property-read Firebase firebase
 * @property-read callable repository
 * @property-read Token token
 * @property-read string uid Firebase UID
 * @property-read Logger logger
 * @property-read string publicKey
 * @property-read TableQuery tableQuery
 *
 */
abstract class BaseRepository
{

    /**
     * @var Query
     */
    protected $query;

    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var Container
     */
    private $container;

    /**
     * BaseRepository constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->query = $container->offsetGet('db');
        $this->pdo = $this->query->getPdo();
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function __get($name)
    {
        return $this->getContainer()->get($name);
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param $repositoryClass
     * @return BaseRepository
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function getRepository($repositoryClass): BaseRepository
    {
        return ($this->container->get('repository'))($repositoryClass);
    }

    /**
     * @param $repositoryClass
     * @return BaseRepository
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function cast($repositoryClass): BaseRepository
    {
        return $this;
    }

    /**
     * @param $sql
     * @return int
     */
    public function execute($sql)
    {
        return $this->pdo->exec($sql);
    }

    /**
     * @param $sql
     * @param null $fetch_style
     * @return mixed
     */
    public function fetch($sql, $fetch_style = null)
    {
        return $this->query($sql)->fetch($fetch_style);
    }

    /**
     * @param $sql
     * @return false|\PDOStatement
     */
    public function query($sql)
    {
        return $this->pdo->query($sql);
    }

    /**
     * @param $sql
     * @return int
     */
    public function count($sql)
    {
        return $this->query($sql)->rowCount();
    }

    /**
     * @param $db
     * @param $table
     * @param $data
     * @return array
     */
    public function getRequiredColumns($db, $table, $data)
    {
        return array_diff_key($this->getColumnInfo($db, $table), $data);
    }

    /**
     * @param $db
     * @param $table
     * @return array
     */
    public function getColumnInfo($db, $table)
    {
        $sql = <<<END
SELECT
COLUMN_NAME,DATA_TYPE
FROM information_schema.`columns`
WHERE 
TABLE_SCHEMA = '$db' 
AND TABLE_NAME = '$table' 
AND IS_NULLABLE = 'NO'
AND COLUMN_DEFAULT IS NULL
AND EXTRA != 'auto_increment'
END;
        return $this->fetchAll($sql, PDO::FETCH_KEY_PAIR);
    }

    /**
     * @param $sql
     * @param null $fetch_style
     * @return array
     */
    public function fetchAll($sql, $fetch_style = null)
    {
        return $this->query($sql)->fetchAll($fetch_style);
    }


}