<?php

namespace gamringer\JSONPointer;

use gamringer\JSONPointer\Access\Accesses;

class ReferencedValue
{
    private $owner;
    private $token;
    private $accessor;

    private $isNext = false;

    public function __construct(&$owner, $token = null, Accesses $accessor = null)
    {
        $this->owner = &$owner;
        $this->token = $token;
        $this->accessor = $accessor;

        $this->assertPropertiesAccessible();

        if ($token == '-' && $this->isIndexedArray($owner)) {
            $this->isNext = true;
        }
    }

    protected function assertPropertiesAccessible()
    {
        if ($this->accessor === null && $this->token !== null) {
            throw new Exception('Properties are not accessible');
        }
    }

    public function getValue()
    {
        $this->assertElementExists();

        $this->assertNotNext();

        if ($this->token === null) {
            return $this->owner;
        }

        return $this->accessor->getValue($this->owner, $this->token);
    }

    public function setValue($value)
    {
        if ($this->isNext) {
            $this->owner[] = $value;

            return $this;
        }

        if ($this->token === null) {
            $this->owner = $value;

            return $this;
        }

        $this->accessor->setValue($this->owner, $this->token, $value);

        return $this;
    }

    public function insertValue($value)
    {
        if ($this->insertReplaces()) {
            return $this->setValue($value);
        }

        $this->assertInsertableToken();

        $before = array_splice($this->owner, 0, $this->token);

        $this->owner = array_merge($before, [$value], $this->owner);

        return $this;

    }

    private function assertInsertableToken()
    {
        if (!(array_key_exists($this->token, $this->owner) || $this->token == sizeof($this->owner))) {
            throw new Exception('Only an integer index can be inserted to an array');
        }
    }

    private function insertReplaces()
    {
        return $this->isNext || !$this->isIndexedArray($this->owner);
    }

    public function unsetValue()
    {
        $this->assertElementExists();

        $this->assertNotNext();

        if ($this->token === null) {
            $this->owner = null;

            return $this;
        }

        $this->accessor->unsetValue($this->owner, $this->token);

        return $this;
    }

    private function assertNotNext()
    {
        if ($this->isNext) {
            throw new Exception('Referenced next value can only be set');
        }
    }

    private function assertElementExists()
    {
        if ($this->token === null || $this->isNext) {
            return;
        }

        if (!$this->accessor->hasValue($this->owner, $this->token)) {
            throw new Exception('Referenced value does not exist');
        }
    }

    private function isIndexedArray($value)
    {
        if (!is_array($value)) {
            return false;
        }

        $count = sizeof($value);
        $value[] = null;

        $result = array_key_exists($count, $value);

        array_pop($value);

        return  $result;
    }
}
