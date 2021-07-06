<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class SecurityController extends AbstractController
{
    private const SERIALIZATION_GROUPS = ["partner_get"];

    /**
     * @Route("/partners/login", name="partner_login", methods={"POST"})
     */
    public function login(Request $request): JsonResponse
    {
        $user = $this->getUser();

        return $this->json($user, 200, [], [AbstractNormalizer::GROUPS => self::SERIALIZATION_GROUPS]);
    }

}
