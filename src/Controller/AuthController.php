<?php

namespace App\Controller;

use App\Service\JWTAuth;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    /**
     * @Route("/auth", name="auth", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('auth/index.html.twig');
    }

    /**
     * @Route("/auth", name="login", methods={"POST"})
     */
    public function login(Request $request, JWTAuth $auth)
    {
        return $auth->auth($request);
    }
}
