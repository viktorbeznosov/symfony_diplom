<?php
declare(strict_types=1);

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class UserAccountProfileFormModel
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;

    public $firstName;

    /**
     * @Assert\Length(min="6", minMessage="Пароль должен быть не менее 6 символов")
     */
    public $password;

    public $confirmPassword;

    /**
     * @Assert\Callback()
     *
     * @param mixed $payload
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
