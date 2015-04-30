<?php

namespace gamringer\JSONPointer\Access;

class ObjectAccessor implements Accesses
{
    public function &getValue(&$target, $token)
    {
        $pointedValue = &$target->{$token};
        
        return $pointedValue;
    }
    
    public function &setValue(&$target, $token, &$value)
    {
        $target->{$token} = &$value;
        
        return $this->getValue($target, $token);
    }

    public function unsetValue(&$target, $token)
    {
        unset($target->{$token});
    }
    
    public function hasValue(&$target, $token)
    {
        return property_exists($target, $token);
    }
}
