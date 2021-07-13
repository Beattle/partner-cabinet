<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Swagger\Annotations as SWG;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups as SerializeGroup;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="partner")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 *     fields="email",
 *     message="Этот email уже зарегистрирован в системе"
 * )
 * @UniqueEntity(
 *     fields="phone",
 *     message="Этот телефон уже зарегистрирован в системе"
 * )
 */
class Partner implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * Адрес email
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     * @Assert\Email()
     * @Assert\NotBlank()
     * @SerializeGroup({"partner_set", "partner_get"})
     */
    public $email;

    /**
     * Мобильный телефон
     * @var string|null
     * @ORM\Column(type="string", length=30, nullable=true, unique=true)
     * @SWG\Property(example="79167231100")
     * @SerializeGroup({"partner_set" ,"partner_get", "partner_update"})
     */
    public $phone;

    /**
     * Имя партнёра
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @SWG\Property(example="Иван")
     * @Assert\NotBlank()
     * @SerializeGroup({"partner_set", "partner_get", "partner_update"})
     */
    public $firstname;

    /**
     * Отчество партнёра
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @SWG\Property(example="Васильевич")
     * @SerializeGroup({"partner_set", "partner_get", "partner_update"})
     */
    public $middlename;

    /**
     * Фамилия партнёра
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @SWG\Property(example="Моргунов")
     * @Assert\NotBlank()
     * @SerializeGroup({"partner_set", "partner_get", "partner_update"})
     */
    public $lastname;

    /**
     * Текстовый пароль пользователя
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min=6,
     *     max=4096,
     *     minMessage="Пароль должен состоять как минимум из 6 символов.",
     *     groups={"Default", "update"}
     * )
     * @SerializeGroup({"partner_set", "partner_update"})
     */
    public $password = null;

    /**
     * @var DateTimeImmutable Дата и время создания партнёра
     * @SWG\Property(example="2018-03-11T10:00:00+03:00")
     * @ORM\Column(type="datetime")
     */
    public $createdAt;

    /**
     * @var DateTimeImmutable Дата и время изменения партнёра
     * @SWG\Property(example="2018-08-28T09:15:16+00:00")
     * @ORM\Column(type="datetime")
     */
    public $updatedAt;

    /**
     * Флаг подтверждения почты
     * @var bool
     * @ORM\Column(type="boolean",  nullable=true)
     */
    public $emailConfirmed = false;

    /**
     * Токен подтверждения почты
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    public $emailConfirmationToken;

    /**
     * Хеш пароля в бд
     * @var string
     * @ORM\Column(type="string")
     */
    public $passwordHash;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @throws Exception
     */
    public function updatedTimestamps()
    {
        $this->updatedAt = new DateTimeImmutable();

        if ($this->createdAt === null) {
            $this->createdAt = new DateTimeImmutable();
        }
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return implode(' ', array_filter([$this->firstname, $this->middlename, $this->lastname]));
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return array<Role|string> The user roles
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        $this->password = null;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string|null The encoded password if any
     */
    public function getPassword(): ?string
    {
        return $this->passwordHash;
    }

    /**
     * @Assert\Callback(groups={"update"})
     * @param ExecutionContextInterface $context
     */
    public function validateBeforeUpdate(ExecutionContextInterface $context)
    {
        if (empty(array_filter(get_object_vars($this)))) {
            $context->buildViolation('Не указаны данные для обновления')
                ->atPath('data')
                ->addViolation();
        }
    }
}
