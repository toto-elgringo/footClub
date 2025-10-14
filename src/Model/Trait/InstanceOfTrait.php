<?php

namespace App\Model\Trait;

trait InstanceOfTrait
{

    protected function checkInstanceOf($object, string $className): bool
    {
        if (!$object instanceof $className) {
            return false;
        }
        
        return true;
    }
}