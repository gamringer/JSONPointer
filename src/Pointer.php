<?php

namespace gamringer\JSONPointer;

use gamringer\JSONPointer\Access\Accesses;
use gamringer\JSONPointer\Access\ArrayAccessor;
use gamringer\JSONPointer\Access\ObjectAccessor;

class Pointer
{
    private $target;

    private $arrayAccessor;
    private $stdObjectAccessor;
    private $objectAccessors = [];

    public function __construct(&$target = null)
    {
        if ($target !== null) {
            $this->setTarget($target);
        }
    }

    protected function getArrayAccessor()
    {
        if (!isset($this->arrayAccessor)) {
            $this->arrayAccessor = new ArrayAccessor();
        }

        return $this->arrayAccessor;
    }

    protected function getStdObjectAccessor()
    {
        if (!isset($this->stdObjectAccessor)) {
            $this->stdObjectAccessor = new ObjectAccessor();
        }

        return $this->stdObjectAccessor;
    }

    public function setAccessor($type, Accesses $accessor)
    {
        $this->objectAccessors[$type] = $accessor;
    }

    public function setTarget(&$target)
    {
        $this->target = &$target;
    }

    public function getTarget()
    {
        return $this->target;
    }

    private function reference($path)
    {
        $this->assertTarget();

        $path = $this->getCleanPath($path);
        if (empty($path)) {
            return new ReferencedValue($this->target);
        }

        return $this->walk($path);
    }

    public function get($path)
    {
        return $this->reference($path)->getValue();
    }

    public function set($path, $value)
    {
        $this->reference($path)->setValue($value);

        return $this;
    }

    public function insert($path, $value)
    {
        $this->reference($path)->insertValue($value);

        return $this;
    }

    public function remove($path)
    {
        $this->reference($path)->unsetValue();

        return $this;
    }

    private function unescape($token)
    {
        $token = (string) $token;

        if (preg_match('/~[^01]/', $token)) {
            throw new Exception('Invalid pointer syntax');
        }

        $token = str_replace('~1', '/', $token);
        $token = str_replace('~0', '~', $token);

        return $token;
    }

    private function getCleanPath($path)
    {
        $path = (string) $path;

        $path = $this->getRepresentedPath($path);

        if (!empty($path) && $path[0] !== '/') {
            throw new Exception('Invalid pointer syntax');
        }

        return $path;
    }

    private function getRepresentedPath($path)
    {
        if (substr($path, 0, 1) === '#') {
            return urldecode(substr($path, 1));
        }

        return stripslashes($path);
    }

    private function walk($path)
    {
        $target = &$this->target;
        $tokens = explode('/', substr($path, 1));
        
        $accessor = null;
        
        while (($token = array_shift($tokens)) !== null) {

            $accessor = $this->getAccessorFor($target);
            $token = $this->unescape($token);

            if (empty($tokens)) {
                break;
            }

            $this->assertWalkable($target);
            $target = &$this->fetchTokenTargetFrom($target, $token, $accessor);
        }

        return new ReferencedValue($target, $token, $accessor);
    }

    private function getAccessorFor(&$target)
    {
        switch (gettype($target)) {
            case 'array':
                return $this->getArrayAccessor();

            case 'object':
                foreach ($this->objectAccessors as $class => $objectAccessor) {
                    if ($target instanceof $class) {
                        return $objectAccessor;
                    }
                }
                return $this->getStdObjectAccessor();
        }
    }

    private function &fetchTokenTargetFrom(&$target, $token, Accesses $accessor)
    {
        $result = &$accessor->getValue($target, $token);

        return $result;
    }

    private function assertWalkable($item)
    {
        switch (gettype($item)) {
            case 'array':
            case 'object':
                return;
        }

        throw new Exception('JSONPointer can only walk through Array or handled Object instances');
    }

    private function assertTarget()
    {
        if (!isset($this->target)) {
            throw new Exception('No target defined');
        }
    }
}
