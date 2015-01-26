<?php

namespace gamringer\JSONPointer;

class ReferencedValue
{
    private $owner;
    private $token;

    private $isNext = false;

    public function __construct(&$owner, $token = null)
    {
        $this->owner = &$owner;
        $this->token = $token;

        if ($token == '-' && $this->isSingleDimensionArray($owner)) {
            $this->isNext = true;
        }

        $this->assertElementExists();
    }

    public function getValue()
    {
        $this->assertNotNext();

        if ($this->token === null) {
            return $this->owner;
        }

        return $this->owner[$this->token];
    }

    public function setValue($value)
    {
        if($this->isNext){

            $this->owner[] = $value;

            return $this;
        }

        if ($this->token === null) {
            $this->owner = $value;

            return $this;
        }

        $this->owner[$this->token] = $value;

        return $this;
    }

    public function unsetValue()
    {
        $this->assertNotNext();

        if ($this->token === null) {
            $this->owner = null;

            return $this;
        }

        unset($this->owner[$this->token]);

        return $this;
    }

    private function assertNotNext()
    {
        if($this->isNext){
            throw new Exception('Referenced next value can only be set');
        }
    }

    private function assertElementExists()
    {
        if ($this->token === null || $this->isNext) {
            return;
        }

        if(!isset($this->owner[$this->token])){
            throw new Exception('Referenced value does not exist');
        }
    }

    private function isSingleDimensionArray($array)
    {
        if(!is_array($array)){
            return false;
        }

        foreach (array_keys($array) as $key) {
            if (!is_int($key)) {
                return false;
            }
        }

        return true;
    }
}
