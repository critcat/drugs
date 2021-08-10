<?php

namespace App\Service;

use App\Model\UserModel;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class JWTAuth
{
    private UserModel $userModel;
    private RouterInterface $router;

    public function __construct(UserModel $userModel, RouterInterface $router)
    {
        $this->userModel = $userModel;
        $this->router = $router;
    }

    public function auth(Request $request): RedirectResponse
    {
        $username = $request->request->get('username');
        $password = $request->request->get('password');

        $token = $this->userModel->login($username, $password);

        $request->getSession()->set('token', $token);

        if (($target = $request->getSession()->get('target_url'))
            && $target !== $this->router->generate('auth', [], UrlGeneratorInterface::ABSOLUTE_URL)) {
            return new RedirectResponse($target);
        }

        return new RedirectResponse($this->router->generate('main'));
    }
}