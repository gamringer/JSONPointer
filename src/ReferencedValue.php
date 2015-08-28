<?php

namespace gamringer\JSONPointer;

use gamringer\JSONPointer\Access\Accesses;
use gamringer\JSONPointer\Access\ArrayAccessor;

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

        if ($token == '-' && $accessor instanceof ArrayAccessor && $accessor->isIndexedArray($owner)) {
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

    public function setValue(&$value)
    {
        if ($this->isNext) {
            $this->owner[] = &$value;

            return new VoidValue($this->owner, sizeof($this->owner)-1);
        }

        if ($this->token === null) {
            $previousValue = $this->owner;

            $this->owner = $value;

            return $previousValue;
        }

        $previousValue = $this->accessor->getValue($this->owner, $this->token);

        $this->accessor->setValue($this->owner, $this->token, $value);

        return $previousValue;
    }

    public function insertValue($value)
    {
        if ($this->insertReplaces()) {
            return $this->setValue($value);
        }

        $this->assertInsertableToken();

        array_splice($this->owner, $this->token, 0, $value);

        return new VoidValue($this->owner, $this->token);
    }

    private function assertInsertableToken()
    {
        if (!(array_key_exists($this->token, $this->owner) || $this->token == sizeof($this->owner))) {
            throw new Exception('Index is out of range');
        }
    }

    private function insertReplaces()
    {
        return $this->isNext
            || !($this->accessor instanceof ArrayAccessor)
            || !$this->accessor->isIndexedArray($this->owner)
            || (is_array($this->owner) && filter_var($this->token, FILTER_VALIDATE_INT) === false)
        ;
    }

    public function unsetValue()
    {
        $this->assertElementExists();

        $this->assertNotNext();

        if ($this->token === null) {
            $previousValue = $this->owner;

            $this->owner = new VoidValue();

            return $previousValue;
        }

        $previousValue = $this->accessor->getValue($this->owner, $this->token);

        $this->accessor->unsetValue($this->owner, $this->token);

        return $previousValue;
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
}
