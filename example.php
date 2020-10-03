<?php

declare(strict_types=1);
//require_once('vendor/autoload.php');


/*
class A{
    public array $store;
    public function __construct(array &$store)
    {
        $this->store = &$store;
    }
    public function get():?int{
       return $this->store['key'];
    }
    public function set(string $key,int $value){
        $this->store[$key]=$value;
    }
}

$blobal_store = ['key'=>4];

$a = new A($blobal_store);
assert($a->get()==4);

  $blobal_store['key'] = 6;
assert($a->get()==6);

  $a->set('key',8);
assert($a->get()==8);

assert(  $blobal_store['key']==8);

*/


class Store{
    private array $refSession = [];
    public function __construct(array &$refSession)
    {
        $this->refSession = &$refSession;
    }
    public function &getSession():?array
    {
        return $this->refSession;
    }
}


class Temp{
    public function abc(Store $store){

        $session = &$store->getSession();
        $session['key'] = 123;
    }
}


$refSession = [];
$store = new Store($refSession);
$temp = new Temp();
$temp->abc($store);
print_r($refSession);