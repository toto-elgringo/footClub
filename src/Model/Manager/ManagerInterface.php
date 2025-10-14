<?php

namespace App\Model\Manager;

interface ManagerInterface
{
    public function findAll(): array;
    public function findById(int $id): ?object;
    public function insert(object $object): bool;
    public function delete(object $object): bool;
    public function update(object $object): bool;
}
