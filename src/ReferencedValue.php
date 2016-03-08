<?php
declare(strict_types=1);

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

        $this->assertAccessorCovers();

        if ($token == '-' && $this->isIndexedArray()) {
            $this->isNext = true;
        }
    }

    public function getValue()
    {
        $this->assertElementExists();

        if ($this->token === null) {
            return $this->owner;
        }

        return $this->accessor->getValue($this->owner, $this->token);
    }

    public function setValue(&$value)
    {
        if ($this->isNext) {
            $this->owner[] = &$value;

            return new VoidValue($this->owner, (string)(sizeof($this->owner)-1));
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

        array_splice($this->owner, (int)$this->token, 0, $value);

        return new VoidValue($this->owner, $this->token);
    }

    public function unsetValue()
    {
        $this->assertElementExists();

        if ($this->token === null) {
            $previousValue = $this->owner;

            $this->owner = new VoidValue();

            return $previousValue;
        }

        $previousValue = $this->accessor->getValue($this->owner, $this->token);

        $this->accessor->unsetValue($this->owner, $this->token);

        return $previousValue;
    }
    
    protected function assertAccessorCovers()
    {
        if ($this->accessor === null) {
            return;
        }

        if (!$this->accessor->covers($this->owner)) {
            throw new Exception('Provided Accessor does not handle owner');
        }
    }

    protected function assertPropertiesAccessible()
    {
        if ($this->accessor === null && $this->token !== null) {
            throw new Exception('Properties are not accessible');
        }
    }

    private function assertInsertableToken()
    {
        if (!(array_key_exists($this->token, $this->owner) || $this->token == sizeof($this->owner))) {
            throw new Exception('Index is out of range');
        }
    }

    private function insertReplaces(): bool
    {
        return $this->isNext
            || filter_var($this->token, FILTER_VALIDATE_INT) === false
            || !$this->isIndexedArray()
        ;
    }

    private function isIndexedArray(): bool
    {
        return $this->accessor instanceof ArrayAccessor
            && $this->accessor->isIndexedArray($this->owner);
    }

    private function assertElementExists()
    {
        $this->assertOwnerExists();

        if ($this->token === null) {
            return;
        }

        if (!$this->accessor->hasValue($this->owner, $this->token)) {
            throw new Exception('Referenced value does not exist');
        }
    }

    private function assertOwnerExists()
    {
        if ($this->owner instanceof VoidValue) {
            throw new Exception('Referenced value does not exist');
        }
    }
}
