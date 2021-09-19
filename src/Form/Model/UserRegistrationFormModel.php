<?php

namespace App\Form\Model;

use App\Validator\UniqueUser;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class UserRegistrationFormModel
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @UniqueUser()
     */
    public $email;
    public $firstName;
    /**
     * @Assert\NotBlank(message="Пароль не указан")
     * @Assert\Length(min="6", minMessage="Пароль должен быть не менее 6 символов")
     */
    public $password;

    public $confirmPassword;

    /**
     * @Assert\Callback()
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {

        if ($this->confirmPassword != $this->password) {
            $context->buildViolation('Введенные пароли не совпадают!')
                ->atPath('confirmPassword')
                ->addViolation();
        }
    }

}