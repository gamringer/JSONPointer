<?php

include __DIR__.'/../../vendor/autoload.php';

class Foo
{
    private $varA;
    private $bar;

    public function getVarA()
    {
        return $this->varA;
    }

    public function getBar()
    {
        return $this->bar;
    }

    public function setVarA($value)
    {
        $this->varA = $value;
    }

    public function setBar(Bar $bar)
    {
        $this->bar = $bar;
    }
}

class Bar
{
    private $varB;

    public function getVarB()
    {
        return $this->varB;
    }

    public function setVarB($value)
    {
        $this->varB = $value;
    }
}

class GetterAccessor implements \gamringer\JSONPointer\Access\Accesses
{
    public function &getValue(&$target, $token)
    {
        $pointedValue = new \gamringer\JSONPointer\VoidValue($target, $token);

        $normalizedToken = preg_replace_callback('/(^|[\._\- ]+)[a-z]/', function($match){return strtoupper($match[0][-1]);}, $token);
        $getter = 'get'.$normalizedToken;

        if ($this->hasValue($target, $token)) {
            $pointedValue = $target->$getter();
        }
        
        return $pointedValue;
    }
    
    public function &setValue(&$target, $token, &$value)
    {
        $normalizedToken = preg_replace_callback('/(^|[\._\- ]+)[a-z]/', function($match){return strtoupper($match[0][-1]);}, $token);
        $setter = 'set'.$normalizedToken;
        $target->$setter($value);
        
        return $this->getValue($target, $token);
    }

    public function unsetValue(&$target, $token)
    {
        $normalizedToken = preg_replace_callback('/(^|[\._\- ]+)[a-z]/', function($match){return strtoupper($match[0][-1]);}, $token);
        $setter = 'set'.$normalizedToken;
        $target->$setter(null);
    }
    
    public function hasValue(&$target, $token)
    {
        return property_exists($target, $token);
    }

    public function covers(&$target)
    {
        return is_object($target);
    }
}

$bar = new Bar();
$bar->setVarB('B');

$foo = new Foo();
$foo->setVarA('A');
$foo->setBar($bar);

$pointer = new \gamringer\JSONPointer\Pointer($foo);
$pointer->getAccessorCollection()->setAccessor(Foo::class, new GetterAccessor());
$pointer->getAccessorCollection()->setAccessor(Bar::class, new GetterAccessor());
echo $pointer->get('/varA');
$newValue = 'Z';
echo $pointer->set('/varA', $newValue);
echo $pointer->get('/varA');

echo $pointer->get('/bar/varB');

?>

