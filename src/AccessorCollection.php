<?php

namespace gamringer\JSONPointer;

use gamringer\JSONPointer\Access\Accesses;
use gamringer\JSONPointer\Access\ArrayAccessor;
use gamringer\JSONPointer\Access\ObjectAccessor;

class AccessorCollection
{
    private static $arrayAccessor;
    private static $stdObjectAccessor;
    private $objectAccessors = [];

    protected function getArrayAccessor()
    {
        if (!isset(static::$arrayAccessor)) {
            static::$arrayAccessor = new ArrayAccessor();
        }

        return static::$arrayAccessor;
    }

    protected function getStdObjectAccessor()
    {
        if (!isset(static::$stdObjectAccessor)) {
            static::$stdObjectAccessor = new ObjectAccessor();
        }

        return static::$stdObjectAccessor;
    }

    protected function getObjectAccessor($target)
    {
        foreach ($this->objectAccessors as $class => $objectAccessor) {
            if ($target instanceof $class) {
                return $objectAccessor;
            }
        }
        return $this->getStdObjectAccessor();
    }

    public function setAccessor($type, Accesses $accessor)
    {
        $this->objectAccessors[$type] = $accessor;
    }

    public function getAccessorFor(&$target)
    {
        switch (gettype($target)) {
            case 'array':
                return $this->getArrayAccessor();

            case 'object':
                return $this->getObjectAccessor($target);
        }
    }
}
