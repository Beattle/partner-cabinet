<?php

namespace App\Tests\Helper\DataCreation;

use App\Entity\Partner as EntityPartner;
use Nalogka\Codeception\Database\DataCreatorModuleInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @see \App\Tests\BackendTester::amLoggedAt
 */
class Partner extends AbstractBase implements DataCreatorModuleInterface
{

    public function getDataCreator(): array
    {
        return [$this, 'hasPartner'];
    }

    public function getNameVariants(): array
    {
        return [
            'партнер',
            'партнера',
            'партнером',
        ];
    }

    public function getDataClass(): string
    {
        return EntityPartner::class;
    }

    /**
     * @Given /существует партнер "([^"]+?)"(?: с почтой "([^"]+?)"|)(?: с телефоном (\+[1-9]{1}[0-9]{3,14}$)|)(?: с токеном email "([^"]+?)"|)(?: с паролем "([^"]+?)"|)(?: с токеном пароля (\d{4})|)$/
     * @param string $name
     */
    public function hasPartner(
        string $name = '',
        $email = '',
        $phone = '',
        $emailConfirmationToken = '',
        $password = null,
        $passwordResetToken = ''

    ) {
        $partnerID = mb_substr(crc32($name), 0, 8);
        $partner = new EntityPartner();
        $partner->id = $partnerID;
        $partner->email = $email ?: $this->faker->email;
        $partner->phone = $phone ?: $this->faker->e164PhoneNumber;
        $partner->firstname = $this->faker->firstName;
        $partner->lastname = $this->faker->lastName;
        $partner->middlename = $this->faker->word();
        $partner->emailConfirmationToken = $emailConfirmationToken ?: '';
        $partner->passwordResetToken = $passwordResetToken ?: '';
        $symfony = $this->getModule('Symfony');
        /** @var UserPasswordEncoderInterface $encoder */
        $encoder = $symfony->grabService('security.password_encoder');
        $partner->passwordHash = $encoder->encodePassword($partner, $password ?? $this->faker->numerify('######'));
        $partner->passwordResetToken = $passwordResetToken;

        $this->data->persistAndRegisterCreated('партнер', $name, $partner);
    }
}
