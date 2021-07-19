<?php

namespace App\Controller;

use App\Entity\Partner;
use App\Entity\Request\ConfirmResetPasswordRequest;
use App\Repository\PartnerRepository;
use App\Traits\ValidatorTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

class PartnerPasswordConfirmController extends AbstractController
{
    use ValidatorTrait;

    /**
     * @var PartnerRepository
     */
    private $partnerRepository;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var AuthenticationManagerInterface
     */
    private $authenticationManager;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(
        SerializerInterface $serializer,
        PartnerRepository $partnerRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        AuthenticationManagerInterface $authenticationManager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->serializer = $serializer;
        $this->partnerRepository = $partnerRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
    }

    /**
     * Редактирование профиля партнера
     * @Route("/partners/reset-password-confirm", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPasswordConfirm(Request $request): JsonResponse
    {
        if (empty($request->getContent())) {
            throw new BadRequestHttpException('Не указаны данные для обновления');
        }
        $partnerData = $this->serializer->deserialize(
            $request->getContent(),
            ConfirmResetPasswordRequest::class,
            'json'
        );

        $this->validateAndThrowError($partnerData);

        $partner = $this->confirmPassword($partnerData);

        $this->loginByUser($partner);

        return $this->json(['status' => true]);
    }

    private function loginByUser(Partner $user): void
    {
        $token = new UsernamePasswordToken(
            $user,
            null,
            'main',
            $user->getRoles()
        );

        $authenticatedToken = $this
            ->authenticationManager
            ->authenticate($token);

        $this->tokenStorage->setToken($authenticatedToken);
    }

    private function confirmPassword($partnerData)
    {
        $partner = $this->getDoctrine()->getRepository(Partner::class)->findOneBy(
            ['passwordResetToken' => $partnerData->passwordResetToken]
        );
        if (!$partner) {
            throw new AccessDeniedHttpException('Не найден пользователь с указанным токеном');
        }

        $partner->passwordHash = $this->passwordEncoder->encodePassword($partner, $partnerData->password);
        $partner->passwordResetToken = null;

        $this->getDoctrine()->getManager()->flush();

        return $partner;
    }
}
