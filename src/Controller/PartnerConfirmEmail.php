<?php

namespace App\Controller;

use App\Entity\Partner;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class PartnerConfirmEmail extends AbstractController
{
    /**
     * @var AuthenticationManagerInterface
     */
    private $authenticationManager;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;


    public function __construct(
        AuthenticationManagerInterface $authenticationManager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->authenticationManager = $authenticationManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/partners/email-confirm", name="email_confirmation", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function confirmAction(Request $request): JsonResponse
    {
        $token = $request->request->get('email_confirm_token');
        if (empty($token)) {
            throw new NotFoundHttpException('Не указан токен для подтверждения email');
        }
        $partner = $this->confirmEmail($token);
        if ($partner) {
            $this->loginByUser($partner);
        }

        return $this->json(["status" => true]);
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

    /**
     * @param string $token
     * @return Partner
     */
    private function confirmEmail(string $token): Partner
    {
        $partner = $this->getDoctrine()->getRepository(Partner::class)->findOneBy(
            ['emailConfirmationToken' => $token]
        );
        if (!$partner) {
            throw new NotFoundHttpException('Не найден пользователь для данного токена');
        }
        $partner->emailConfirmationToken = null;
        $partner->emailConfirmed = true;
        $em = $this->getDoctrine()->getManager();
        $em->persist($partner);
        $em->flush();

        return $partner;
    }

}
