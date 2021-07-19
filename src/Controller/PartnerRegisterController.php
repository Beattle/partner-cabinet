<?php

namespace App\Controller;

use App\Entity\Partner;
use App\Service\EmailService;
use App\Traits\ValidatorTrait;
use Nalogka\UniqueStringGenerator\Generator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class PartnerRegisterController extends AbstractController
{
    use ValidatorTrait;

    private const DESERIALIZATION_GROUPS = ["partner_set"];

    private const SERIALIZATION_GROUPS = ["partner_get"];

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var string
     */
    private $cabinetFrontUrl;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;


    public function __construct(
        string $cabinetFrontUrl,
        SerializerInterface $serializer,
        EmailService $emailService,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->cabinetFrontUrl = $cabinetFrontUrl;
        $this->serializer = $serializer;
        $this->emailService = $emailService;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/partners/register", name="partner_register", methods={"POST"})
     * @throws \Exception
     */
    public function register(Request $request): JsonResponse
    {
        /** @var Partner $partner */
        $partner = $this->serializer->deserialize(
            $request->getContent(),
            Partner::class,
            'json',
            [AbstractNormalizer::GROUPS => self::DESERIALIZATION_GROUPS]
        );

        $this->validateAndThrowError($partner);

        $this->registerPartner($partner);

        return $this->json($partner, 200, [], [AbstractNormalizer::GROUPS => self::SERIALIZATION_GROUPS]);
    }

    /**
     * @throws \Exception
     */
    private function registerPartner(Partner $partner): Partner
    {
        $partner->emailConfirmationToken = Generator::generate(Generator::DEC_ALPHA_L_U, 40);
        $emailToken = $partner->emailConfirmationToken;
        $this->emailService->send(
            $partner->email,
            'email/registration.twig',
            [
                'name' => $partner->getFullName(),
                'confirmUrl' => $this->cabinetFrontUrl . "/email-confirm/?token=$emailToken",
            ]
        );

        $partner->passwordHash = $this->passwordEncoder->encodePassword($partner, $partner->password);
        $em = $this->getDoctrine()->getManager();
        $em->persist($partner);
        $em->flush();

        return $partner;
    }
}
