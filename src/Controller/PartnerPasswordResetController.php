<?php

namespace App\Controller;

use App\Entity\Partner;
use App\Entity\Request\ResetPasswordRequest;
use App\Repository\PartnerRepository;
use App\Service\EmailService;
use App\Traits\ValidatorTrait;
use Nalogka\UniqueStringGenerator\Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PartnerPasswordResetController extends AbstractController
{
    use ValidatorTrait;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var PartnerRepository
     */
    private $partnerRepository;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var string
     */
    private $cabinetFrontUrl;

    public function __construct(
        string $cabinetFrontUrl,
        EmailService $emailService,
        SerializerInterface $serializer,
        PartnerRepository $partnerRepository
    ) {
        $this->cabinetFrontUrl = $cabinetFrontUrl;
        $this->serializer = $serializer;
        $this->partnerRepository = $partnerRepository;
        $this->emailService = $emailService;
    }

    /**
     * Редактирование профиля партнера
     * @Route("/partners/reset-password-request", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function resetPasswordRequest(Request $request): JsonResponse
    {
        if (empty($request->getContent())) {
            throw new BadRequestHttpException('Не указаны данные для обновления');
        }
        $partnerData = $this->serializer->deserialize(
            $request->getContent(),
            ResetPasswordRequest::class,
            'json'
        );

        $this->validateAndThrowError($partnerData);

        $partner = $this->getDoctrine()->getRepository(Partner::class)->findOneBy(
            ['email' => $partnerData->email]
        );

        if (!$partner) {
            throw new NotFoundHttpException('Не найден пользователь с указанным email');
        }

        $partner->passwordResetToken = Generator::generate(Generator::DEC, 4);

        $this->emailService->send(
            $partner->email,
            'email/pass_reset.twig',
            [
                'name' => $partner->getFullName(),
                'confirmUrl' => $this->cabinetFrontUrl . "/reset-password/?token=$partner->passwordResetToken",
            ]
        );

        $this->getDoctrine()->getManager()->flush();

        return $this->json(['status' => true]);
    }
}
