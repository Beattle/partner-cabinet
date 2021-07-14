<?php

namespace App\Controller;

use App\Entity\Partner;
use App\Traits\ValidatorTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class PartnerUpdateController extends AbstractController
{
    use ValidatorTrait;

    private const DESERIALIZATION_GROUPS = ['partner_update'];

    private const SERIALIZATION_GROUPS = ["partner_get"];

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(
        SerializerInterface $serializer,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->serializer = $serializer;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Редактирование профиля партнера
     * @Route("/partners/update", methods={"POST"})
     * @return JsonResponse
     */
    public function updatePartner(Request $request): JsonResponse
    {
        if (empty($request->getContent())) {
            throw new BadRequestHttpException('Не указаны данные для обновления');
        }
        $partnerData = $this->serializer->deserialize(
            $request->getContent(),
            Partner::class,
            'json',
            [AbstractNormalizer::GROUPS => self::DESERIALIZATION_GROUPS]
        );

        $this->validateAndThrowError($partnerData, ['update']);

        $partner = $this->updatePartnerFields($partnerData);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->json($partner, 200, [], [AbstractNormalizer::GROUPS => self::SERIALIZATION_GROUPS]);
    }

    private function updatePartnerFields($partnerData): Partner
    {
        /** @var Partner $partner */
        $partner = $this->getUser();

        if ($partnerData->password) {
            $partner->passwordHash = $this->passwordEncoder->encodePassword($partner, $partnerData->password);
        }
        $partner->phone = $partnerData->phone;
        $partner->firstname = $partnerData->firstname;
        $partner->middlename = $partnerData->middlename;
        $partner->lastname = $partnerData->lastname;

        return $partner;
    }
}
