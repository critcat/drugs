<?php

namespace App\Controller;

use App\Service\ApiRequester;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SubstanceController extends AbstractController
{
    /**
     * @Route("/substance", name="substance_list")
     */
    public function list(ApiRequester $apiRequester)
    {
        $response = $apiRequester->request('GET', '/api/substances');
        $content = $response->toArray();

        return $this->render('substance_list.html.twig', [
            'substances' => $content['hydra:member']
        ]);
    }
}