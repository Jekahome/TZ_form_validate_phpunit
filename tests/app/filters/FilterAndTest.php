<?php
declare(strict_types=1);

namespace tests\app\filters;

use PHPUnit\Framework\TestCase;
use DMS\PHPUnitExtensions\ArraySubset\Assert;

use app\filters\FilterAnd;
use app\BuildFilter;

final class FilterAndTest extends TestCase
{

// <метод>_<cценарий>_<результат>

    public function testValidateResultEmptySuccess()
    {
        $filter = new FilterAnd();
        $result = $filter->validate();
        TestCase::assertIsArray($result);
        TestCase::assertEmpty($result);
    }

    public function testValidateBuildFilterSuccess()
    {
        $filter = new FilterAnd();
        $build = new BuildFilter('');
        $filter->addFilter($build);
        $result = $filter->validate();
        TestCase::assertIsArray($result);
        TestCase::assertEmpty($result);
    }

    public function testValidateStateSuccess()
    {
        $filter = new FilterAnd();
        $filter->validate();
        TestCase::assertTrue($filter->isValidate());
    }

    public function testValidateDoubleUsedExeption()
    {
        $filter = new FilterAnd();
        $filter->validate();
        TestCase::expectException(\ErrorException::class);
        $filter->validate();
    }

    public function testValidateMessageErrorFailed()
    {
        $filter = new FilterAnd();
        $error = "error message";
        $key = "login";
        $build = new BuildFilter($key, $error);
        $build->addWork(function () {
            return false;
        });
        $filter->addFilter($build);
        $result = $filter->validate();

        Assert::assertArraySubset([0=>[$key=>$error]], $result, true);

        TestCase::assertArrayHasKey(0, $result);
        TestCase::assertArrayHasKey($key, $result[0]);
        TestCase::assertEquals($result[0][$key], $error);
    }

    public function testValidateMessageSuccessSuccess()
    {
        $filter = new FilterAnd();
        $success= "success message";
        $key = "login";
        $build = new BuildFilter($key, "",$success);
        $build->addWork(function () {
            return true;
        });
        $filter->addFilter($build);
        $result = $filter->validate();

        Assert::assertArraySubset([0=>[$key=>$success]], $result, true);

        TestCase::assertArrayHasKey(0, $result);
        TestCase::assertArrayHasKey($key, $result[0]);
        TestCase::assertEquals($result[0][$key], $success);
    }
    public function testValidateAlgorithmAndFailed()
    {
        $filter = new FilterAnd();
        $build = new BuildFilter("");
        $build->addWork(function () {
            return true;
        });
        $build->addWork(function () {
            return false;
        });
        $build->addWork(function () {
            return true;
        });
        $filter->addFilter($build);
        $filter->validate();

        TestCase::assertFalse($filter->isValidate());
    }

    public function testValidateAlgorithmAndSuccess()
    {
        $filter = new FilterAnd();
        $error = "error message";
        $key = "login";
        $build = new BuildFilter($key, $error);
        $build->addWork(function () {
            return true;
        });
        $build->addWork(function () {
            return true;
        });
        $build->addWork(function () {
            return true;
        });
        $filter->addFilter($build);
        $filter->validate();

        TestCase::assertTrue($filter->isValidate());
    }
}
