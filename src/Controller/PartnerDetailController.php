<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class PartnerDetailController extends AbstractController
{
    private const SERIALIZATION_GROUPS = ["partner_get"];

    /**
     * @Route("/partners/info", name="partner_detail", methods={"GET"})
     * @return JsonResponse
     */
    public function partnerDetail(): JsonResponse
    {
        $user = $this->getUser();

        return $this->json($user, 200, [], [AbstractNormalizer::GROUPS => self::SERIALIZATION_GROUPS]);
    }
}
