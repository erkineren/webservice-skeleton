<?php


namespace App\Database\Adapter;


interface IAdapter
{
    public function getTable(): string;

    public function find($columns = ['*'], IFilter $filter = null): array;

    public function findAll($columns = ['*'], IFilter $filter = null): array;

    public function delete(IFilter $filter = null): bool;

    public function update(array $data, IFilter $filter = null): bool;

    public function insert(array $data): bool;


}