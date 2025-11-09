<?php

namespace App\Model\Manager;

interface ManagerInterface
{
    public function findAll(): array;
    public function insert(object $object): bool;
    public function delete(object $object): bool;
}
