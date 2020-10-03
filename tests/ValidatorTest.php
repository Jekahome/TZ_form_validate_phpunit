<?php

declare(strict_types=1);

namespace tests\app\filters;

use app\language\language;
use PHPUnit\Framework\TestCase;
use DMS\PHPUnitExtensions\ArraySubset\Assert;

use app\Validator;

final class ValidatorTest extends TestCase
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
        $validator->validate();
        TestCase::assertTrue($validator->isValidate());
    }

    public function testActivateFieldFailed()
    {
        TestCase::expectException(\RuntimeException::class);
        TestCase::expectExceptionMessage("Field not found");
        $validator = new Validator();
        $validator->activateField('','');
    }

    public function testActivateFieldSuccess()
    {
        $validator = new Validator();
        $validator->activateField('name','Bob');
        TestCase::assertEquals($validator->name,'Bob');
        TestCase::assertIsArray($validator->validate());
    }

}
