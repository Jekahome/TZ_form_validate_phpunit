<?php

declare(strict_types=1);

use app\language\language;
use app\UserData;
use PHPUnit\Framework\TestCase;
use DMS\PHPUnitExtensions\ArraySubset\Assert;

use app\User;

// Запуск с --globals-backup --static-backup

/**
 * @runTestsInSeparateProcesses
 */
final class UserTest extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private static bool $init = false;
    private static array $refSessionStoreStatic=[];
    private static array $refCookieStoreStatic=[];

    public function setUp():void
    {
        $refSessionLanguage = [];
        $refCookieLanguage = [];
        $httpAcceptLanguage = "";

        $lang = language::init();
        $lang->initStore($refSessionLanguage, $refCookieLanguage, $httpAcceptLanguage);
        $lang->__destruct();
        unset($lang);

        if (!UserTest::$init) {
            UserTest::$init = true;

            Mockery::getConfiguration()->allowMockingNonExistentMethods(true);

            $externalStoreService = Mockery::mock('overload:app\StoreService');

            $externalStoreService->shouldReceive('setSession')->withArgs(function ($key, $value) {
                UserTest::$refSessionStoreStatic[$key]=$value;
                return true;
            });
            $externalStoreService->shouldReceive('getSessionValue')->andReturnUsing(function ($key) {
                return  UserTest::$refSessionStoreStatic[$key]??null;
            });
            $externalStoreService->shouldReceive('unsetSessionValue')->withArgs(function ($key) {
                unset(UserTest::$refSessionStoreStatic[$key]);
                return true;
            });
            $externalStoreService->shouldReceive('setCookie')->withArgs(function ($key, $value) {
                UserTest::$refCookieStoreStatic[$key]=$value;
                return true;
            });
            $externalStoreService->shouldReceive('getCookieValue')->andReturnUsing(function ($key) {
                return UserTest::$refCookieStoreStatic[$key]??null;
            });
            $externalStoreService->shouldReceive('unsetCookieValue')->withArgs(function ($key) {
                unset(UserTest::$refCookieStoreStatic[$key]);
                return true;
            });
        }
    }

    public static function setUpBeforeClass():void
    {
        require_once('define.php');
        if (!defined('TEST')) {
            define('TEST', true);
        }
    }

    public function tearDown():void
    {
        Mockery::close();
        User::destruct();
    }

    public function testAuthFalseSuccess()
    {
        UserTest::$refSessionStoreStatic = [];
        UserTest::$refCookieStoreStatic = [];

        $user = User::getInstance();
        TestCase::assertFalse($user->isAuth());
        TestCase::assertNull($user->getUri());
    }

    public function testAuthTrueSuccess()
    {
        $id = 1;
        $hashkey = "sdkfjdkslsnvkjdfnv";

        UserTest::$refSessionStoreStatic = [$hashkey=>$id];
        UserTest::$refCookieStoreStatic = ["UID"=>$hashkey];

        $user = User::getInstance();
        TestCase::assertTrue($user->isAuth());
        TestCase::assertEquals($user->getUri(), md5((string)$id.SALT_USER_PAGE));
    }

    public function testLogoutSuccess()
    {
        $id = 1;
        $hashkey = "sdkfjdkslsnvkjdfnv";

        UserTest::$refSessionStoreStatic = [$hashkey=>$id];
        UserTest::$refCookieStoreStatic = ["UID"=>$hashkey];

        $user = User::getInstance();
        TestCase::assertTrue($user->isAuth());
        TestCase::assertEquals($user->getUri(), md5((string)$id.SALT_USER_PAGE));

        $user->logout();
        TestCase::assertFalse($user->isAuth());
        TestCase::assertArrayNotHasKey($hashkey, UserTest::$refSessionStoreStatic);
        TestCase::assertArrayNotHasKey("UID", UserTest::$refCookieStoreStatic);
    }

    public function testLoginSuccess()
    {
        $id = 1;
        UserTest::$refSessionStoreStatic = [];
        UserTest::$refCookieStoreStatic = [];
        $user = User::getInstance();

        $stub = Mockery::mock('app\UserData');
        $stub->id = $id;
        $stub->shouldReceive('getUserEmail')->andReturnSelf();

        $result = $user->login($stub);
        TestCase::assertTrue($user->isAuth());
        TestCase::assertEquals($user->getUri(), md5((string)$id.SALT_USER_PAGE));
        TestCase::assertEquals($result, md5((string)$id.SALT_USER_PAGE));
    }

    public function testPostWithoutFileSuccess()
    {
        $dbUser=['id'=>1,'email'=>'email@email.ua','name'=>'Jeka'];
        $hashkey = "sdkfjdkslsnvkjdfnv";

        UserTest::$refSessionStoreStatic = [$hashkey=>$dbUser['id']];
        UserTest::$refCookieStoreStatic = ["UID"=>$hashkey];

        $stub = Mockery::mock('app\UserData');
        $stub->shouldReceive('createUser')
            ->with(Mockery::type('string'))->andReturn($dbUser['id']);
        $stub->email = $dbUser['email'];
        $stub->name = $dbUser['name'];

        $user = User::getInstance();
        $result = $user->post($stub);
        TestCase::assertTrue($result);
    }

    public function testPostWithFileSuccess()
    {
        TestCase::markTestIncomplete('Этот тест ещё не реализован.');
    }

    public function testPutSuccess()
    {
        $password = password_hash('123456', PASSWORD_BCRYPT, ['cost'=>12]);

        $currentUser=['id'=>1,'email'=>'email@email.ua','name'=>'Jeka','login'=>'login','password'=>$password];
        $url = md5((string)$currentUser['id'].SALT_USER_PAGE);

        $hashkey = "sdkfjdkslsnvkjdfnv";
        UserTest::$refSessionStoreStatic = [$hashkey=>$currentUser['id']];
        UserTest::$refCookieStoreStatic = ["UID"=>$hashkey];

        $extendStub = Mockery::mock('overload:app\UserData');
        $extendStub->email = $currentUser['email'];
        $extendStub->name = $currentUser['name'];
        $extendStub->password = "";
        $extendStub->shouldReceive('getUserWithPassword')->with($currentUser['id'])->andReturn($currentUser);
        $extendStub->shouldReceive('updateUser')->with(true)->andReturn(true);
        $extendStub->shouldReceive('getUserWithPassword')->with($url)->andReturn($currentUser);

        $user = User::getInstance();
        $result = $user->put($extendStub);
        TestCase::assertTrue($result);
        TestCase::assertEquals($extendStub->password, $password);
        TestCase::assertEquals($extendStub->id, $currentUser['id']);
    }

    public function testPutPasswordUpdateSuccess()
    {
        $currentUser=['id'=>1,'email'=>'email@email.ua','name'=>'Jeka','login'=>'login','password'=>"456456456"];
        $url = md5((string)$currentUser['id'].SALT_USER_PAGE);

        $hashkey = "sdkfjdkslsnvkjdfnv";
        UserTest::$refSessionStoreStatic = [$hashkey=>$currentUser['id']];
        UserTest::$refCookieStoreStatic = ["UID"=>$hashkey];

        $extendStub = Mockery::mock('overload:app\UserData');
        $extendStub->email = $currentUser['email'];
        $extendStub->name = $currentUser['name'];
        $extendStub->password = "456456456";
        $extendStub->shouldReceive('getUserWithPassword')->with($currentUser['id'])->andReturn($currentUser);
        $password = password_hash('456456456', PASSWORD_BCRYPT, ['cost'=>12]);
        $extendStub->shouldReceive('passwordHash')->with($currentUser['password'])->andReturn($password);
        $extendStub->shouldReceive('updateUser')->with(true)->andReturn(true);
        $extendStub->shouldReceive('getUserWithPassword')->with($url)->andReturn($currentUser);

        $user = User::getInstance();
        $result = $user->put($extendStub);
        TestCase::assertTrue($result);
        TestCase::assertEquals($extendStub->id, $currentUser['id']);
        TestCase::assertEquals($extendStub->password, $password);
    }

    public function testGetSuccess()
    {
        $id = 1;
        $hashkey = "sdkfjdkslsnvkjdfnv";
        UserTest::$refSessionStoreStatic = [$hashkey=>$id];
        UserTest::$refCookieStoreStatic = ["UID"=>$hashkey];

        $url = md5((string)$id.SALT_USER_PAGE);
        $getUser = ['id'=>$id,'name'=>'Jeka','email'=>'email@email.ua','login'=>'login'];

        $extendStub = Mockery::mock('overload:app\UserData');
        $extendStub->shouldReceive('getUser')
            ->with($id)->andReturn($getUser);

        $user = User::getInstance();
        $result = $user->get($url);
        TestCase::assertEquals($getUser, $result);
    }

    public function testGetNoAuthFailed()
    {
        $id = 1;
        $hashkey = "sdkfjdkslsnvkjdfnv";
        UserTest::$refSessionStoreStatic = [];
        UserTest::$refCookieStoreStatic = ["UID"=>$hashkey];

        $url = md5((string)$id.SALT_USER_PAGE);
        $getUser = ['id'=>$id,'name'=>'Jeka','email'=>'email@email.ua','login'=>'login'];

        $extendStub = Mockery::mock('overload:app\UserData');
        $extendStub->shouldReceive('getUser')
            ->with($id)->andReturn($getUser);

        $user = User::getInstance();
        $result = $user->get($url);
        TestCase::assertEquals(null, $result);
    }

    public function testGetFakeUrlFailed()
    {
        $id = 1;
        $hashkey = "sdkfjdkslsnvkjdfnv";
        UserTest::$refSessionStoreStatic = [$hashkey=>$id];
        UserTest::$refCookieStoreStatic = ["UID"=>$hashkey];

        $urlFake = md5((string)"2".SALT_USER_PAGE);
        $getUser = ['id'=>$id,'name'=>'Jeka','email'=>'email@email.ua','login'=>'login'];

        $extendStub = Mockery::mock('overload:app\UserData');
        $extendStub->shouldReceive('getUser')
            ->with($id)->andReturn($getUser);

        $user = User::getInstance();
        $result = $user->get($urlFake);
        TestCase::assertEquals(null, $result);
    }

}

/*
 * 1. Класс User статический и после инициализации getInstance, его свойство $instance становится обьектом User.
 * Если его не сбросить в null то при следующем тесте свойство будет мешать для этого вызываем статический деструктов для сброса его в null каждый новый тест
 * для етого использую setUp но это работает и с setUpBeforeClass который вызывается всего один раз !!! Как сбрасывается свойство $instance ???
 * Через восстановление --global-backup ???
 *
 * 2. Почему создание overload class в методе setUpBeforeClass не сохраняется  после первого их использования ?
 */
