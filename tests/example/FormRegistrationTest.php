<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use app\forms\FormRegistration;



class OriginDbAPI{
    private \PDO $db;
    private string $api;
    public function __construct()
    {

        $this->db = new  PDO("mysql:host=127.0.0.1;dbname=dbform;charset=UTF8;port=3306", "jeka","jeka" );
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function newUser(){
        $stmt =  $this->db->prepare("INSERT INTO users(`name`,email,login,password,token,confirmed,create_time) VALUES(:name,:email,:login,:password,:token,:confirmed,:create_time);");

        $stmt->bindValue(':name', 'name', PDO::PARAM_STR);
        $stmt->bindValue(':email', 'email@mail.ua', PDO::PARAM_STR);
        $stmt->bindValue(':login', 'login', PDO::PARAM_STR);
        $stmt->bindValue(':password', 'password', PDO::PARAM_STR);
        $stmt->bindValue(':token', 'token', PDO::PARAM_STR);
        $stmt->bindValue(':confirmed', false, PDO::PARAM_BOOL);
        $stmt->bindValue(':create_time', time(), PDO::PARAM_INT);

        $stmt->execute();

// INSERT INTO users(`name`,email,login,password,token,confirmed,create_time) VALUES('name','email','login','password','token',0,1600372464);
    }

    public function getUser(string $email):array {
        if(filter_var( $email, FILTER_VALIDATE_EMAIL) == false)return [];

        $stmt = $this->db->prepare('SELECT `name`,email,login FROM users WHERE email=:email;');
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
       if($stmt->execute()){
           $result = $stmt->fetchAll();
           return $result;
       }
        return [];
    }
}

class OriginClass
{
    private string $name;
    public function __construct(string $name = "Bob")
    {
        $this->name = $name;
    }
    public function getName():string{
        return $this->name;
    }
    private function setName(string $name):void{
        $this->name = $name;
    }
}
// Заглушки Stubs - Это тестовый двойник  Test Doubles для проверки возвращаемых значений
// Подставные объекты , имитациея (mocking) - Это тестовый двойник  Test Doubles для проверки входных и возвращаемых значений

final class FormRegistrationTest extends TestCase
{
    // Заглушки Stubs ==============================


    public function testStubCreateMock()
    {
        // «За кулисами» PHPUnit автоматически генерирует новый PHP-класс, который реализует желаемое поведение
        // Создать заглушку для класса OriginClass.
        $stub = TestCase::createMock(OriginClass::class);

        // вариант 1----------------------
        // Настроить заглушку.
        // $stub->method('getName')->willReturn('Regina');
        // TestCase::assertSame('Regina', $stub->getName());

        // вариант 2-----------------------
        // Или еще тупее вариант, вернуть первый аргумент
        // Так же возможно не существующий метод использовать, будет предупреждение
        $stub->method('getName')->will(TestCase::returnArgument(0));
        // Это для тупого варианта с возвратом первого аргумента, который даже в OriginClass не принимает аргументы !!!
        TestCase::assertSame('Regina', $stub->getName('Regina'));
        //---------------------------------

        // Если исходный класс объявляет метод, названный «method», тогда для проверки утверждения нужно использовать
        //$stub->expects(TestCase::any())->method('getName')->willReturn('Regina');
    }

    // другой вариант конструктора класса заглушки getMockBuilder
    public function testStubGetMockBuilder()
    {
        // Создать заглушку для класса SomeClass.
        $stub = TestCase::getMockBuilder(OriginClass::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        // Настроить заглушку.
        $stub->method('getName')
            ->willReturn('Regina');

        // Вызов $stub->doSomething() теперь вернёт 'foo'.
        TestCase::assertSame('Regina', $stub->getName());
    }

    //  возвращать разные значения в зависимости от предопределённого списка аргументов.
    public function testReturnValueMapStub()
    {
        // Создать заглушку для класса OriginClass.
        $stub = TestCase::createMock(OriginClass::class);

        // Создать карту аргументов для возврата значений
        // первыми идут обязательные аргументы ,а последним в массиве идет возвращаемый результат
        $map = [
            ['a', 'b', 'c', 'result d'],
            ['e',  'result h']
        ];

        // Настроить заглушку.
        $stub->method('getName')->will(TestCase::returnValueMap($map));

        // Или возвращать по порядку вызова, переданные значения не учитываются
        //$stub->method('getName')->will(TestCase::onConsecutiveCalls('result d','result h'));

        // $stub->getName() возвращает разные значения в зависимости от предоставленного списка.
        TestCase::assertSame('result d', $stub->getName('a', 'b', 'c'));
        TestCase::assertSame('result h', $stub->getName('e'));
    }


    public function testReturnCallbackStub()
    {
        $stub = TestCase::createMock(OriginClass::class);

        $stub->method('getName')->will(TestCase::returnCallback('mb_strtoupper'));

        // можно вернуть исключение
        //$stub->method('getName')->will(TestCase::throwException(new Exception));

        // Вызов $stub->getName($argument) вернёт mb_strtoupper($argument)
        TestCase::assertSame('BOB', $stub->getName('bob'));
    }

    // Подставные объекты , имитациея (mocking) =============================




}
