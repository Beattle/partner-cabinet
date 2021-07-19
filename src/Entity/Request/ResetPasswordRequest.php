<?php

namespace App\Entity\Request;

use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordRequest
{
    /**
     * Адрес email
     * @var string
     * @Assert\Email()
     * @Assert\NotBlank()
     */
    public $email;
}
