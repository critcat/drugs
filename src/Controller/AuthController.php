<?php

namespace App\Controller;

use App\Service\ApiRequester;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    /**
     * @Route("/auth", name="auth")
     */
    public function index(Request $request, ApiRequester $apiRequester, SessionInterface $session): Response
    {
        if ($request->isMethod('POST')) {
            try {
                $username = $request->request->get('username');
                $password = $request->request->get('password');

                $token = $apiRequester->login($username, $password);

                $session->set('token', $token);

                if ($target = $session->get('target_url')) {
                    return $this->redirect($target);
                }

                return $this->redirectToRoute('main');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('auth/index.html.twig');
    }
}
