<?php

namespace gamringer\JSONPointer\Access;

interface Accesses
{
    public function &getValue(&$target, $token);

    public function &setValue(&$target, $token, &$value);
    
    public function unsetValue(&$target, $token);

    public function hasValue(&$target, $token);

    public function covers(&$target);
}
