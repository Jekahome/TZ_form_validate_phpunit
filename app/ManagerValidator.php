<?php
declare(strict_types=1);

namespace app;

use app\AbstractValidator;

class ManagerValidator
{
    private bool $isValidate;
    private array $errors;
    private array $success;
    private array $filters;

    public function __construct()
    {
        $this->isValidate = false;
        $this->filters = [];
        $this->errors = [];
        $this->success = [];
    }

    public function add(AbstractValidator $filter)
    {
        array_push($this->filters, $filter);
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function validate():array
    {
        $this->isValidate = false;
        $this->errors = [];
        $this->success = [];
        $isValidate = true;
        $resultErrors = [];
        $resultSuccess= [];

        if (!empty($this->filters)) {
            $key = array_key_first($this->filters);
            $this->filters[$key]->reset();
        }

        foreach ($this->filters as $filter) {
            $temp = $filter->validate();
            if ($filter->isValidate()==false) {
                $isValidate = false;
                foreach ($temp as $data) {
                    array_push($resultErrors, $data);
                }
            } else {
                foreach ($temp as $data) {
                    array_push($resultSuccess, $data);
                }
                if (!$filter->currentValidate()) {
                    $isValidate = false;
                }
            }
        }
        $this->isValidate = $isValidate;

        foreach ($resultErrors as $value) {
            $key = array_key_first($value);
            if (!isset($this->errors[$key])) {
                $this->errors[$key]=[];
            }
            array_push($this->errors[$key], $value[$key]);
        }
        foreach ($this->errors as &$sub) {
            $sub = array_unique($sub);
        }
        unset($resultErrors);

        foreach ($resultSuccess as $value) {
            $key = array_key_first($value);
            if (!isset($this->success[$key])) {
                $this->success[$key]=[];
            }
            array_push($this->success[$key], $value[$key]);
        }
        foreach ($this->success as &$sub) {
            $sub = array_unique($sub);
        }

        unset($resultSuccess);

        if ($this->isValidate) {
            return $this->success;
        }
        return $this->errors;
    }

    public function isValidate():bool
    {
        return $this->isValidate;
    }

    public function getErrors():array
    {
        return $this->errors;
    }

    public function getSuccess():array
    {
        return $this->success;
    }
}
