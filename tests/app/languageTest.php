<?php

declare(strict_types=1);

namespace tests\app\filters;

use PHPUnit\Framework\TestCase;
use DMS\PHPUnitExtensions\ArraySubset\Assert;

use app\language\language;

final class languageTest extends TestCase
{
    // <метод>_<cценарий>_<результат>

    public static function setUpBeforeClass():void
    {
        require_once('define.php');
        if(!defined('TEST'))
            define('TEST', true);
    }

    public function setUp():void{
        $refSession = [];
        $refCookie = [];
        $httpAcceptLanguage = "";

        $lang = language::init();
        $lang->initStore($refSession, $refCookie, $httpAcceptLanguage);
        $lang->__destruct();
        unset($lang);
    }


   public function testInitGetLibraryFailed()
    {
        TestCase::expectException(\RuntimeException::class);
        TestCase::expectExceptionMessage("Data source not initialized");
        $lang = language::init();
        $lang->getLibrary("index");
        $lang->__destruct();
    }

    public function testInitSetLanguageFailed()
    {
        TestCase::expectException(\RuntimeException::class);
        TestCase::expectExceptionMessage("Data source not initialized");
        $lang = language::init();
        $lang->setLanguage("ru");
        $lang->__destruct();
    }

    public function testDefaultSuccess()
    {
        $refSession = [];
        $refCookie = [];
        $httpAcceptLanguage = "ru,en;q=0.9,ru-RU;q=0.8";

        $lang = language::init();
        $lang->initStore($refSession, $refCookie, $httpAcceptLanguage);
        $lang->__destruct();

        TestCase::assertArrayHasKey('lang', $refSession);
        TestCase::assertArrayHasKey('default lang', $refSession);
        TestCase::assertEquals($refSession['lang'], DEFAULT_LANG);
        TestCase::assertEquals($refSession['default lang'], DEFAULT_LANG);
    }

    public function testDefaultLangSuccess()
    {
        $refSession = [];
        $refCookie = [];
        $httpAcceptLanguage = "--,en;q=0.9,ru-RU;q=0.8";

        $lang = language::init();
        $lang->initStore($refSession, $refCookie, $httpAcceptLanguage);
        $lang->__destruct();

        TestCase::assertArrayHasKey('lang', $refSession);
        TestCase::assertArrayHasKey('default lang', $refSession);
        TestCase::assertEquals($refSession['lang'], DEFAULT_LANG);
        TestCase::assertEquals($refSession['default lang'], DEFAULT_LANG);
    }

    public function testInitCookieSuccess()
    {
        $refSession = [];
        $refCookie = ['lang'=>'en'];
        $httpAcceptLanguage = "";

        $lang = language::init();
        $lang->initStore($refSession, $refCookie, $httpAcceptLanguage);
        $lang->__destruct();

        TestCase::assertArrayHasKey('lang', $refSession);
        TestCase::assertEquals($refSession['lang'], 'en');
    }

    public function testSetLanguageSuccess()
    {
        $refSession = [];
        $refCookie = [];
        $httpAcceptLanguage = "";

        $lang = language::init();
        $lang->initStore($refSession, $refCookie, $httpAcceptLanguage);

        TestCase::assertArrayHasKey('lang', $refSession);
        TestCase::assertEquals($refSession['lang'], DEFAULT_LANG);
        TestCase::assertCount(0, $refCookie);

        $lang->setLanguage('en');
        $lang->__destruct();
        TestCase::assertEquals($refSession['lang'], 'en');
        TestCase::assertArrayHasKey('lang', $refCookie);
        TestCase::assertEquals($refCookie['lang'], 'en');
    }
}
