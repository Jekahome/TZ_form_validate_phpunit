<?php
declare(strict_types=1);

namespace app\forms;

use app\AbstractValidator;
use app\AbstractForm;
use app\ManagerValidator;
use app\BuildFilter;
use app\filters\FilterAnd;
use app\IValidator;
use app\Validator;

class FormLogin implements AbstractForm
{
    public string $email;
    public string $password;
    private IValidator $validator;

    public function __construct(IValidator $validator)
    {
        $this->validator = $validator;
    }

    public function post(array $input):array
    {
        try {
            $this->email = $input['email']??'';
            $this->password = $input['password']??'';

            $this->validator =  new Validator();

            $this->validator->activateField('email', $this->email);
            $this->validator->activateField('password', $this->password);

            $errors = $this->validate();
            return $errors;
            /*if (!$this->validator->isValidate()) {
                // render with errors
                echo __CLASS__." - Validation error\n\n";
                throw  new \InvalidArgumentException(sprintf(
                    'errors:"%s" ',
                    __CLASS__
                ));
                return $errors;
            }
            echo __CLASS__." - Validation success\n\n";
            return [];*/
        } catch (\Throwable $e) {
            throw new \RuntimeException("Error post processing");
            //return [];
        }
    }

    public function validate():array
    {
        return $this->validator->validate();
    }

    public function isValidate():bool
    {
        return $this->validator->isValidate();
    }

    public static function create(): self
    {
        return new self();
    }
}
