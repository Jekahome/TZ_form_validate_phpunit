<?php
declare(strict_types=1);

namespace app\filters;

use \ErrorException;
use app\AbstractValidator;
use app\ReturnValue;
use app\IBuildFilter;

class FilterOr extends AbstractValidator
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
            throw new ErrorException("повторное использование фильтра");
            return [];
        }
        $this->validateFinished = true;
        try {
            if (empty($this->filters)) {
                $this->isValid = true;
                return [];
            }
            $res = false;
            $resultErrors = [];
            $resultSuccess = [];
            foreach ($this->filters as $data) {
                $key = array_key_first($data);
                $this->resturnValue = $data[$key]($this);

                if ($this->resturnValue->isValidate()==false) {
                    array_push($resultErrors, [$key=>$this->resturnValue->getMessage()]);
                } else {
                    array_push($resultSuccess, [$key=>$this->resturnValue->getMessage()]);
                    $res = true;
                }
            }
            if ($res==true) {
                $this->isValid = true;
                return $resultSuccess;
            }
            $this->invalidate();
            $this->isValid = false;
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
