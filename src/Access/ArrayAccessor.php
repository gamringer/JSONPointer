<?php

namespace gamringer\JSONPointer\Access;

use gamringer\JSONPointer\VoidValue;

class ArrayAccessor implements Accesses
{
    public function &getValue(&$target, $token)
    {
        $pointedValue = new VoidValue($target, $token);
        
        if ($this->hasValue($target, $token)) {
            $pointedValue = &$target[$token];
        }
        
        return $pointedValue;
    }
    
    public function &setValue(&$target, $token, &$value)
    {
        $target[$token] = &$value;
        
        return $this->getValue($target, $token);
    }

    public function unsetValue(&$target, $token)
    {
        if (!$this->isIndexedArray($target)) {
            unset($target[$token]);
            return;
        }

        array_splice($target, $token, 1);
    }
    
    public function hasValue(&$target, $token)
    {
        return array_key_exists($token, $target);
    }

    public function isIndexedArray(Array $value)
    {
        $count = sizeof($value);
        $value[] = null;

        $result = array_key_exists($count, $value);

        array_pop($value);

        return  $result;
    }

    public function covers(&$target)
    {
        return is_array($target);
    }
}
