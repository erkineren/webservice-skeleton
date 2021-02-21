<?php


namespace App\Database\Adapter;


use ArrayIterator;
use Traversable;

class Filter implements IFilter
{
    /** @var Criteria[] */
    protected $criterias;

    /** @var ArrayIterator|Traversable */
    protected $iterator;

    /**
     * Filter constructor.
     * @param Criteria[] $criterias
     */
    public function __construct(array $criterias = [])
    {
        $this->criterias = $criterias;
        $this->iterator = new ArrayIterator($this->criterias);
    }


    public function addCriteria(Criteria $criteria)
    {
        $this->getIterator()->append($criteria);
    }

    /**
     * @return ArrayIterator|Traversable
     */
    public function getIterator()
    {
        return $this->iterator;
    }
}