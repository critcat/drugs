<?php

namespace App\Controller;

use App\Service\ApiRequester;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ManufacturerController extends AbstractController
{
    /**
     * @Route("/manufacturer", name="manufacturer_list")
     */
    public function index(ApiRequester $apiRequester): Response
    {
        $response = $apiRequester->request('GET', '/api/manufacturers');
        $content = $response->toArray();

        return $this->render('manufacturer_list.html.twig', [
            'manufacturers' => $content['hydra:member'],
        ]);
    }
}
