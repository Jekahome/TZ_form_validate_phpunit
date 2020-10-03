<?php

declare(strict_types=1);

namespace tests\app\filters;

use app\BuildFilter;
use app\filters\FilterOr;
use app\filters\FilterAnd;
use app\ManagerValidator;

use PHPUnit\Framework\TestCase;
use DMS\PHPUnitExtensions\ArraySubset\Assert;

final class ManagerValidatorTest extends TestCase
{
    public function testEmptyValidateSuccess()
    {
        $manager = new ManagerValidator();
        $manager->validate();
        TestCase::assertTrue($manager->isValidate());
        TestCase::assertCount(0, $manager->getErrors());
    }

    public function testAddErrorMessageFailed()
    {
        $filter = new FilterAnd();
        $error = "error message";
        $key = "login";
        $build = new BuildFilter($key, $error);
        $build->addWork(function () {
            return false;
        });
        $filter->addFilter($build);

        $filter2 = new FilterOr();
        $build2 = new BuildFilter("password", "");
        $build2->addWork(function () {
            return false;
        });
        $build2->addWork(function () {
            return true;
        });
        $filter2->addFilter($build2);


        $manager = new ManagerValidator();
        $manager->add($filter);
        $manager->add($filter2);

        $result = $manager->validate();

        TestCase::assertArrayHasKey($key, $result);
        TestCase::assertArrayHasKey(0, $result[$key]);
        TestCase::assertEquals($result[$key][0], $error);
        Assert::assertArraySubset([$key=>[0=>$error]], $result, true);
        TestCase::assertFalse($manager->isValidate());
    }

    public function testTwoFiltersErrorMessageFailed()
    {
        $filter = new FilterAnd();
        $error = "error message";
        $key = "login";
        $build = new BuildFilter($key, $error);
        $build->addWork(function () {
            return false;
        });
        $filter->addFilter($build);

        $filter2 = new FilterOr();
        $error2 = "error message 2";
        $key2 = "password";
        $build2 = new BuildFilter($key2, $error2);
        $build2->addWork(function () {
            return false;
        });
        $build2->addWork(function () {
            return false;
        });
        $filter2->addFilter($build2);

        $manager = new ManagerValidator();
        $manager->add($filter);
        $manager->add($filter2);
        $result = $manager->validate();

        TestCase::assertArrayHasKey($key, $result);
        TestCase::assertArrayHasKey(0, $result[$key]);
        TestCase::assertEquals($result[$key][0], $error);
        Assert::assertArraySubset([$key=>[0=>$error]], $result, true);

        TestCase::assertArrayHasKey($key2, $result);
        TestCase::assertArrayHasKey(0, $result[$key2]);
        TestCase::assertEquals($result[$key2][0], $error2);
        Assert::assertArraySubset([$key2=>[0=>$error2]], $result, true);
        TestCase::assertFalse($manager->isValidate());
    }


    public function testTwoFiltersErrorMessageSuccess()
    {
        $key = "login";
        $success= "success message";
        $build = new BuildFilter($key, "", $success);
        $build->addWork(function () {
            return true;
        });

        $filter = new FilterAnd();
        $filter->addFilter($build);

        $manager = new ManagerValidator();
        $manager->add($filter);

        $result = $manager->validate();

        TestCase::assertTrue($manager->isValidate());

        TestCase::assertArrayHasKey($key, $result);
        TestCase::assertArrayHasKey(0, $result[$key]);
        Assert::assertArraySubset([$key=>[0=>$success]], $result, true);
        TestCase::assertEquals($result[$key][0], $success);
    }
}
