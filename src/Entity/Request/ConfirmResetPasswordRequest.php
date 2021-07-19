<?php

namespace App\Entity\Request;

use Symfony\Component\Validator\Constraints as Assert;

class ConfirmResetPasswordRequest
{
    /**
     * Текстовый пароль пользователя
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min=6,
     *     max=4096,
     *     minMessage="Пароль должен состоять как минимум из 6 символов."
     * )
     */
    public $password = null;

    /**
     * Токен для сброса пароля партнера
     * @var string
     * @Assert\NotBlank()
     */
    public $passwordResetToken;
}
