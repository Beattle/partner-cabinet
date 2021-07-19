<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{

    /**
     * Creates a Response object to send upon a successful logout.
     *
     * @return Response never null
     */
    public function onLogoutSuccess(Request $request)
    {
        return new JsonResponse(['success' => true], 200);
    }
}
