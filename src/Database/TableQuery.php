<?php


namespace App\Database;


use App\Database\Adapter\BaseQueryAdapter;
use Envms\FluentPDO\Query;

class TableQuery
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * TableQuery constructor.
     * @param Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * @param string $table
     * @return BaseQueryAdapter
     */
    public function of(string $table): BaseQueryAdapter
    {
        return (new BaseQueryAdapter($this->query))->setTable($table);

    }
}