<?php
declare(strict_types=1);

namespace app\filters;

use \ErrorException;
use app\AbstractValidator;
use app\ReturnValue;
use app\IBuildFilter;

class FilterAnd extends AbstractValidator
{
    private array $filters = [];
    private bool $isValid = false;
    private ReturnValue $resturnValue;
    private bool $validateFinished = false;

    public function __construct()
    {
        $this->isValid = false;
        $this->validateFinished = false;
        $this->filters = [];
    }

    public function addFilter(IBuildFilter $buildFilter)
    {
        // без интерфейса `IBuildFilter $filter` мы догадываемся в каком формате обьект
        // `BuildFilter $filter` содержит массив callback'ов, что не хорошо

        foreach ($buildFilter->getCallback() as $f) {
            array_push($this->filters, [$buildFilter->getName()=>$f]);
        }

        /* foreach ($build_filter as $f) {
             array_push($this->filters, $f);
         }*/
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function validate():array
    {
        if ($this->validateFinished) {
            throw new ErrorException("filter reuse");
            return [];
        }
        $this->validateFinished = true;
        try {
            $this->isValid = true;
            if (empty($this->filters)) {
                return [];
            }
            $resultErrors = [];
            $resultSuccess = [];
            foreach ($this->filters as $data) {
                $key = array_key_first($data);
                $this->resturnValue = $data[$key]($this);
                if ($this->resturnValue->isValidate()==false) {
                    $this->isValid = false;
                    $this->invalidate();
                    array_push($resultErrors, [$key=>$this->resturnValue->getMessage()]);
                } else {
                    array_push($resultSuccess, [$key=>$this->resturnValue->getMessage()]);
                }
            }
            if ($this->isValid==true) {
                return $resultSuccess;
            }
            return $resultErrors;
        } catch (Throwable $e) {
            $this->invalidate();
            $this->isValid = false;
            return [];
        }
    }

    public function isValidate():bool
    {
        return $this->isValid;
    }
}
