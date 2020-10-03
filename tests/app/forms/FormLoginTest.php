<?php

declare(strict_types=1);

namespace tests\app\filters;

use app\forms\FormLogin;
use app\language\language;
use PHPUnit\Framework\TestCase;
use DMS\PHPUnitExtensions\ArraySubset\Assert;

use app\Validator;

final class FormLoginTest extends TestCase
{
    public static $refLanguage;

    public function setUp():void
    {
        require_once('define.php');
        if (!defined('TEST')) {
            define('TEST', true);
        }

        $refSession = ['lang'=>'ru'];
        $refCookie = [];
        $httpAcceptLanguage = "";
        ValidatorTest::$refLanguage = language::init();
        ValidatorTest::$refLanguage->initStore($refSession, $refCookie, $httpAcceptLanguage);
    }

    public function tearDown():void
    {
        ValidatorTest::$refLanguage ->__destruct();
    }

    public function testInitEmptySuccess()
    {
        $validator = new Validator();
        $form_login = new FormLogin($validator);
        TestCase::assertFalse($form_login->isValidate());
        TestCase::assertIsArray($form_login->validate());
        TestCase::assertTrue($form_login->isValidate());
    }

    public function testInitSuccess()
    {
        $validator = new Validator();
        $form_login = new FormLogin($validator);
        $input = ['email'=>'emai@mail.ua','password'=>'123456'];
        $result = $form_login->post($input);
        TestCase::assertTrue($form_login->isValidate());
        TestCase::assertArrayHasKey('email', $result);
        TestCase::assertArrayHasKey('password', $result);
    }

    public function testEmailIncorrectFailed()
    {
        $validator = new Validator();
        $form_login = new FormLogin($validator);
        $input = ['email'=>'emaimail.ua','password'=>'123456'];
        $result = $form_login->post($input);
        TestCase::assertFalse($form_login->isValidate());
        TestCase::assertArrayHasKey('email', $result);
        TestCase::assertArrayNotHasKey('password',$result);

        $messageField = language::init()->getLibrary('message_field');
        TestCase::assertEquals($messageField['email'],$result['email'][0]);
    }

    public function testPasswordIncorrectFailed()
    {
        $validator = new Validator();
        $form_login = new FormLogin($validator);
        $input = ['email'=>'emai@mail.ua'];
        $result = $form_login->post($input);
        TestCase::assertFalse($form_login->isValidate());
        TestCase::assertArrayHasKey('password', $result);

        $messageField = language::init()->getLibrary('message_field');
        TestCase::assertEquals($messageField['password'],$result['password'][0]);
    }

    public function testArgumentFailed()
    {
        TestCase::expectException(\RuntimeException::class);
        TestCase::expectExceptionMessage("Error post processing");
        $validator = new Validator();
        $form_login = new FormLogin($validator);
        $input = ['email'=>'emai@mail.ua','password'=>123456];
        $form_login->post($input);
    }

    public function testTwoValidateFailed()
    {
        TestCase::expectException(\ErrorException::class);
        TestCase::expectExceptionMessage("filter reuse");
        $validator = new Validator();
        $form_login = new FormLogin($validator);
        $input = ['email'=>'emai@mail.ua','password'=>'123456'];
        $form_login->post($input);
        $form_login->validate();
    }
}

