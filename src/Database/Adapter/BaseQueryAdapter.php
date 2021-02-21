<?php


namespace App\Database\Adapter;


use Envms\FluentPDO\Queries\Common;
use Envms\FluentPDO\Query;
use RuntimeException;

class BaseQueryAdapter implements IAdapter
{
    /** @var Query */
    protected $query;
    /** @var string */
    protected $table;


    /**
     * BaseQueryAdapter constructor.
     * @param Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    public function find($columns = ['*'], IFilter $filter = null): array
    {
        $q = $this->query
            ->from($this->getTable())
            ->select(implode(',', $columns), true);
        $this->addFilterToQuery($q, $filter);
        return $q->fetch();
    }

    public function getTable(): string
    {
        if (!$this->table) throw new RuntimeException("Not implemented table");
        return $this->table;
    }

    /**
     * @param string $table
     * @return $this
     */
    public function setTable(string $table): BaseQueryAdapter
    {
        $this->table = $table;
        return $this;
    }

    public function addFilterToQuery(Common &$query, ?IFilter $filter)
    {
        if ($filter === null) return;
        /** @var Criteria $criteria */
        foreach ($filter as $criteria) {
            if ($filter instanceof AndFilter)
                $query = $query->where($criteria->toString());
            if ($filter instanceof OrFilter)
                $query = $query->whereOr($criteria->toString());
        }
    }

    public function findAll($columns = ['*'], IFilter $filter = null): array
    {
        $q = $this->query
            ->from($this->getTable())
            ->select(implode(',', $columns), true);

        $this->addFilterToQuery($q, $filter);
        return $q->fetchAll();
    }

    public function delete(IFilter $filter = null): bool
    {
        $q = $this->query->delete($this->getTable());
        $this->addFilterToQuery($q, $filter);
        return $q->execute();
    }

    public function update(array $data, IFilter $filter = null): bool
    {
        $q = $this->query->update($this->getTable());
        $this->addFilterToQuery($q, $filter);
        return $q->execute();
    }

    public function insert(array $data): bool
    {
        return $this->query->insertInto($this->getTable(), $data)->execute();
    }
}