<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Mockery\Adapter\Phpunit\MockeryTestCase as MockeryTestCase;// 1. способ. Наследоваться от класса
use app\StoreService;

interface IRead
{
    public function readTemp():int;
}

interface IMath
{
    public function pi():float;
    public function e():float;
}


interface IWith
{
    public function load(int $value):int;
    public function loadTwo(string $value, int $name):int;
    public function loadThree(Three $object);
    public function loadTwo2();
}

class ObjectForSpy implements IWith
{
    public function load(int $value):int
    {
        return 0;
    }
    public function loadTwo(string $value, int $name):int
    {
        return 0;
    }
    public function loadThree(Three $object)
    {
        ;
    }
    public function loadTwo2()
    {
    }
}

interface ISpyEasy
{
    public function fooSpy(int $val):bool;
}

class RuntimePartial
{
    public function foo():bool
    {
        throw new Exception("");
    }
    public function bar():int
    {
        return 8;
    }
}

class FinalPartial
{
    public function foo():bool
    {
        throw new Exception("");
    }
    public function bar():int
    {
        return 8;
    }
}

class ArgConstructor
{
    public string $name;
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function foo():bool
    {
        return $this->name == "Bob";
    }
}

class EasySet
{
    public string $name;
    public function getName():string
    {
        $this->name="Bob";
        return $this->name;
    }
}

class Three
{
    public function work(ChildThree $object):float
    {
        return $object->prop+0.2;
    }
}
class ChildThree
{
    public float $prop;
    public function __construct(float $prop)
    {
        $this->prop = $prop;
    }
}



class Temperature
{
    private $service;
    public string $name;

    public function __construct(IRead $service)
    {
        $this->service = $service;
    }

    public function average():int
    {
        $total = 0;
        for ($i=0; $i<3; $i++) {
            $total += $this->service->readTemp();
        }
        return $total/3;
    }

    public function short(IMath $object):float
    {
        return $object->pi() + $object->e();
    }

    public function with(IWith $object):int
    {
        return $object->load(11)+$object->load(12)+$object->load(13);
    }

    public function withTwo(IWith $object)
    {
        $object->loadTwo2();
    }

    public function withTwo2(IWith $object):int
    {
        return $object->loadTwo("Bob", 1);
    }

    public function loadThree(Three $object):float
    {
        $obj = new ChildThree(1.04);
        return $object->work($obj);
    }

    public function runtimePartial(RuntimePartial $object):?int
    {
        if ($object->foo()) {
            return $object->bar();
        }
        return null;
    }

    public function finalPartial($object):?int
    {
        if ($object->foo()) {
            return $object->bar();
        }
        return null;
    }

    public function argConstructor(ArgConstructor $arg):?string
    {
        if ($arg->foo()) {
            return $arg->name;
        }
        return null;
    }

    public function easyExeption(IRead $objecet):string
    {
        try {
            if ($objecet->readTemp()) {
                return "YES";
            }
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }

    public function easySet(EasySet $object):?string
    {
        assert(!isset($object->name));
        $object->getName();// как только метод отработает, сработает set
        assert($object->name == "Bob");
        return $object->name;
    }

    public function abc($store)
    {
        $refSession2 = ['temp'];
        $refCookie2 = [];
        $store = new StoreService($refSession2, $refCookie2);
        $store->setSession('key', '123');
         print_r([$store->getSessionValue('key')]);

    }
}



final class MockeTest extends PHPUnit\Framework\TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;//2. способ. Наследоваться от трейта

    public static function setUpBeforeClass():void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
    }

    public function tearDown():void
    {
        // Необходимая Интеграция PHPUni
        Mockery::close();
    }

    public function testGetsAverageTemperatureFromThreeServiceReadings()
    {
        // $service = Mockery::mock('service');/* создание двойника */
        $service = Mockery::mock('service, IRead');/* создание двойника реализующего интерфейс*/

        $service->shouldReceive('readTemp')/* ожидание вызова метода */
            ->times(3)/* ожидать 3 вызова метода readTemp */
            ->andReturn(10, 12, 14);/* возвращаемые данные при вызове метода readTemp */

        $temperature = new Temperature($service);

        self::assertEquals(12, $temperature->average());
    }

    public function testShort()
    {
        $class = (new class() implements IRead {
            public function readTemp():int
            {
                return 0;
            }
        });
        $temperature = new Temperature($class);

        // $service = Mockery::mock(array('pi' => 3.1416, 'e' => 2.71),'IMath');
        $service = Mockery::mock('service,IMath');
        $service->shouldReceive('pi')->andReturn(3.1416)->ordered();
        $service->shouldReceive('e')->andReturn(2.71)->ordered();

        self::assertEquals(5.8516, $temperature->short($service));
    }

    // with доработать с разнимы типами
    public function testWith()
    {
        $class = (new class() implements IRead {
            public function readTemp():int
            {
                return 0;
            }
        });
        $temperature = new Temperature($class);

        $service = Mockery::mock('service, IWith');
        $service->shouldReceive('load')
            ->times(3)
            //->with(1)
            //->andReturn(11, 12, 13)
            //->andReturnValues([11, 12, 13])
           /* ->andReturnUsing(function ($arg){
                static $val = 10;
                if($arg == 1){
                    return ++$val;
                }
                return false;
            })*/
          /*  ->andReturnUsing( function ($arg){
                if($arg == 1){
                    return 11;
                }
            },function ($arg){
                if($arg == 1){
                    return 12;
                }
            },function ($arg){
                if($arg == 1){
                    return 13;
                }
            })*/
            ->andReturnArg(0);

        self::assertEquals(36, $temperature->with($service));
    }

    public function testWithTwo()
    {
        $class = (new class() implements IRead {
            public function readTemp():int
            {
                return 0;
            }
        });
        $temperature = new Temperature($class);

        $service = Mockery::mock('service, IWith');
        $service->shouldReceive('loadTwo')
            ->times(1)
            ->with(Mockery::capture($name), Mockery::capture($n))
            //->with("Bob",1)
            //  ->withSomeOfArgs(1,"Bob")
             // ->withAnyArgs()
             // ->with(Mockery::any(),Mockery::any())
            //->withNoArgs()
           // ->withArgs(["Bob",1])
            /*->withArgs(function ($arg1,$arg2){
                if((is_string($arg1) && strstr($arg1,"Bob")) && (is_numeric($arg2) && $arg2 > 0)){
                    return true;
                }
                return false;
            })*/
            /*->with(Mockery::on(function ($arg){
                if(is_string($arg) && strstr($arg,"Bob")){
                    return true;
                }
                return false;
            }),Mockery::on(function ($arg){
                if(is_numeric($arg) && $arg > 0){
                    return true;
                }
                return false;
            }))*/
          //  ->with(Mockery::pattern( '/^[a-zA-Z]{3}/'),Mockery::pattern('/^[1-9]{1}/'))
            ->andReturn(11);

        self::assertEquals(11, $temperature->withTwo2($service));
        self::assertEquals($n, 1);
        self::assertEquals($name, "Bob");
    }

    public function testWithThree()
    {
        $obj = new ChildThree(1.04);

        $service = Mockery::mock('Three');
        $service->shouldReceive('work')
            ->times(1)
            //->with( Hamcrest\Matchers::equalTo($obj)) //  эквивалента == ,проверка типа и полей
            //->with( Hamcrest\Matchers::identicalTo($obj))// аналог with($obj) проверка идентичности ===

              //->with(Mockery::type('ChildThree'))
              ->with(Hamcrest\Matchers::anInstanceOf(ChildThree::class))


            // ->withSomeOfArgs(1,"Bob")
            // ->withAnyArgs()
            // ->with(Mockery::any(),Mockery::any())
            //->withNoArgs()
            //->withArgs([1,"Bob"])
            /*->withArgs(function ($arg){
                if($arg instanceof ChildThree && $arg->prop === 1.04) return true;
                return false;
            })*/
            ->andReturn($obj->prop+0.2);

        $class = (new class() implements IRead {
            public function readTemp():int
            {
                return 0;
            }
        });
        $temperature = new Temperature($class);
        self::assertEquals(1.24, $temperature->loadThree($service));
    }

    public function testRuntimePartial()
    {
        $mock =  Mockery::mock(RuntimePartial::class)->makePartial();
        $mock->shouldReceive('foo')->andReturn(true);// замена метода, в случа использования етого метода реализация будет наша

        $class = (new class() implements IRead {
            public function readTemp():int
            {
                return 0;
            }
        });
        $temperature = new Temperature($class);

        self::assertEquals(8, $temperature->runtimePartial($mock));
    }

    /*public function testFinalPartialSuccess()
    {
        $mock =  Mockery::mock(new FinalPartial);
        $mock->shouldReceive('foo')->andReturn(true);// замена метода, в случа использования етого метода реализация будет наша

        $class = (new class() implements IRead {public function readTemp():int{return 0;}});
        $temperature = new Temperature($class);

        self::assertEquals(8, $temperature->finalPartial($mock));
    }*/

    public function testArgConstructorSuccess()
    {
        $mock =  Mockery::mock('ArgConstructor', ["Bob"]);
        $mock->shouldReceive('foo')->andReturn(true);

        $class = (new class() implements IRead {
            public function readTemp():int
            {
                return 0;
            }
        });
        $temperature = new Temperature($class);

        self::assertEquals("Bob", $temperature->argConstructor($mock));
    }

    public function testExeption()
    {
        $mock = Mockery::mock('service,IRead');
        $mock->shouldReceive('readTemp')
             // ->andThrow(new Exception("My Exeption message"));
                ->andThrow("Exception", "My Exeption message", 444);

        $class = (new class() implements IRead {
            public function readTemp():int
            {
                return 0;
            }
        });
        $temperature = new Temperature($class);

        self::assertEquals("My Exeption message", $temperature->easyExeption($mock));
    }

    public function testEasySet()
    {
        $mock = Mockery::mock('EasySet')
        ->shouldReceive('getName')
             ->passthru()
             ->andReturn("Bob")->getMock();

        $class = (new class() implements IRead {
            public function readTemp():int
            {
                return 0;
            }
        });
        $temperature = new Temperature($class);

        self::assertEquals("Bob", $temperature->easySet($mock));

        self::assertEquals("Bob", $mock->name);
    }

    public function testSpy()
    {
        try {
            //Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
            $class = (new class() implements IRead {
                public function readTemp():int
                {
                    return 0;
                }
            });
            $temperature = new Temperature($class);

            $spy =  Mockery::spy('ObjectForSpy');

            $temperature->withTwo2($spy);// исполнение

            $spy->shouldHaveReceived()->loadTwo('Bob', 1)->with('Bob', 1)->once();//должен был быть вызван метод с такими аргументами один раз
            //$spy->shouldHaveReceived('loadTwo', ['Bob',1]);
            // $spy->shouldHaveReceived('loadTwo2');

            $spy->shouldNotHaveReceived('foo');//не должен быть вызван метод foo
        } catch (Mockery\Exception\BadMethodCallException $e) {
            echo "<<<\n\n".$e->getMessage()."\n\n>>>";
        }
    }

    public function testModel()
    {
        $mock = \Mockery::mock('Model[test]');

        $mock->shouldReceive('test')
            ->with(\Mockery::on(function (&$data) {
                $data['something'] = 'wrong';
                return true;
            }));

        $data = ['foo' => 'bar'];

        $mock->test($data);
        $this->assertTrue(isset($data['something']));
        $this->assertEquals('wrong', $data['something']);
    }

    public function testA()
    {
        $mock = \Mockery::mock('Model');
        $mock->shouldReceive('foo');
        $mock->foo();
    }

    /**
     * @runInSeparateProcess
     * @runTestsInSeparateProcesses
     * @preserveGlobalState disabled
     */
    /* public function testCallingExternalService()
     {
         Mockery::getConfiguration()->allowMockingNonExistentMethods(true);

         $param = 10;

         $externalMock = Mockery::mock('overload:app\External');// создание фиктивного класса
         $externalMock->shouldReceive('__construct')
             ->once()
             ->with(5);
         $externalMock->shouldReceive('sendSomething')
             ->once()
             ->with($param);
         $externalMock->shouldReceive('getSomething')
             ->once()
             ->andReturnUsing(function (){return 50;});

         $service = new Service();

         $result = $service->callExternalService($param);

         TestCase::assertSame(50, $result);
     }*/

    /**
     * @runInSeparateProcess
     * @runTestsInSeparateProcesses
     * @preserveGlobalState disabled
     */
    /*public function testCallingGlobalExternal()
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(true);

        Mockery::getConfiguration()->setConstantsMap([
            'External' => [
                'SESSION' => ['key'=>123]

            ]
        ]);

        $param = 10;

        $externalMock = Mockery::mock('overload:External');

        $service = new Service();
        $result = $service->callGlobalExternal();
        TestCase::assertSame(123, $result);
    }*/

   /* public function testAbc()
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
        $refSession = ['bla'=>444];
        $refCookie = [];
        //ЧАСТИЧНАЯ ЗАМЕНА КОНСТРУКТОРА !!!

        $externalStoreService = Mockery::mock('overload:app\StoreService');

        $externalStoreService->shouldReceive('setSession')->withArgs(function ($key,$value) use(&$refSession) {
             $refSession[$key]=$value;
            return true;
        });
        $externalStoreService->shouldReceive('getSessionValue')->andReturnUsing(function ($key) use (&$refSession) {
            return $refSession[$key]??null;
        });


        $class = (new class() implements IRead {
            public function readTemp():int
            {
                return 0;
            }
        });
        $temperature = new Temperature($class);
        $temperature->abc($externalStoreService);

        print_r([$refSession]);
        print_r([$externalStoreService->getSessionValue('key')]);
        print_r([$externalStoreService->getSession()]);

       // TestCase::assertArrayHasKey('key', $refSession);
       // TestCase::assertEquals($refSession['key'], 123);
        TestCase::assertTrue(true);
    }*/
}



class Store
{
    private array $refSession = [];
    private array $refCookie = [];

    public function __construct(array &$refSession, array &$refCookie)
    {
        $this->refSession = &$refSession;
        $this->refCookie = &$refCookie;
    }

    public function &getSession():?array
    {
        return $this->refSession;
    }

    public function &getCookie():?array
    {
        return $this->refCookie;
    }
}

/*
namespace app;

class External{
    const SESSION = ['key'=>123];
    private int $val=1;
    private int $version;
    public function __construct(int $version)
    {
        $this->version=$version;
    }
    public function sendSomething(int $param){
        $this->val=$param;
    }
    public function getSomething():int{
        return 100;//$this->val * $this->version;
    }
}
*/
class Service
{
    private array $store;

    public function initStore(array &$store)
    {
        $this->store = $store;
    }

    public function callExternalService(int $val):int
    {
        $externalService = new  External($version = 5);
        $externalService->sendSomething($val);
        return $externalService->getSomething();
    }

    public function callGlobalExternal():?int
    {
        if (isset(External::SESSION['key'])) {
            return External::SESSION['key'];
        }
        return null;
    }
}




class Model
{
    public function test(&$data)
    {
        throw new Exception("");
    }

    protected function doTest(&$data)
    {
        $data['something'] = 'wrong';
        return $this;
    }
    public function foo()
    {
    }
}
