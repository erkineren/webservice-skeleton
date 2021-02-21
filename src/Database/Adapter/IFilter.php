<?php


namespace App\Database\Adapter;


use IteratorAggregate;

interface IFilter extends IteratorAggregate
{
    public function addCriteria(Criteria $criteria);
}