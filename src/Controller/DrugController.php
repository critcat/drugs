<?php

namespace App\Controller;

use App\Service\ApiRequester;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/drug")
 */
class DrugController extends AbstractController
{
    private ApiRequester $apiRequester;

    public function __construct(ApiRequester $apiRequester)
    {
        $this->apiRequester = $apiRequester;
    }

    /**
     * @Route(name="drug_list")
     */
    public function index(): Response
    {
        $response = $this->apiRequester->request('GET', '/api/drugs');
        $content = $response->toArray();

        return $this->render('drug/list.html.twig', [
            'drugs' => $content['hydra:member'],
        ]);
    }

    /**
     * @Route("/new", name="drug_new")
     */
    public function new(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            return $this->insertItem($request);
        }

        $response = $this->apiRequester->request('GET', '/api/manufacturers');
        $manufacturers = $response->toArray()['hydra:member'];

        $response = $this->apiRequester->request('GET', '/api/substances');
        $substances = $response->toArray()['hydra:member'];

        return $this->render('drug/insert.html.twig', [
            'manufacturers' => $manufacturers,
            'substances' => $substances,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="drug_delete")
     */
    public function delete($id)
    {
        $this->apiRequester->request(
            'DELETE',
            '/api/drugs/' . $id
        );

        return $this->redirectToRoute('drug_list');
    }

    /**
     * @Route("/{id}", name="drug_edit")
     */
    public function edit($id, Request $request)
    {
        if ($request->getMethod() === 'POST') {
            return $this->updateItem($id, $request);
        }

        return $this->getItem($id);
    }

    private function getItem(int $id)
    {
        $response = $this->apiRequester->request('GET','/api/drugs/' . $id);
        $drugs = $response->toArray();

        $response = $this->apiRequester->request('GET', '/api/manufacturers');
        $manufacturers = $response->toArray()['hydra:member'];

        $response = $this->apiRequester->request('GET', '/api/substances');
        $substances = $response->toArray()['hydra:member'];

        return $this->render('drug/edit.html.twig', [
            'drug' => $drugs,
            'manufacturers' => $manufacturers,
            'substances' => $substances,
        ]);
    }

    private function updateItem(int $id, Request $request)
    {
        $body = [
            'name' => $request->request->get('name'),
            'price' => floatval($request->request->get('price')),
            'manufacturer' => $request->request->get('manufacturer'),
            'substance' => $request->request->get('substance'),
        ];

        $this->apiRequester->request(
            'PUT',
            '/api/drugs/' . $id,
            json_encode($body)
        );

        return $this->redirectToRoute('drug_list');
    }

    private function insertItem(Request $request)
    {
        $body = [
            'name' => $request->request->get('name'),
            'price' => floatval($request->request->get('price')),
            'manufacturer' => $request->request->get('manufacturer'),
            'substance' => $request->request->get('substance'),
        ];

        $this->apiRequester->request(
            'POST',
            '/api/drugs',
            json_encode($body)
        );

        return $this->redirectToRoute('drug_list');
    }
}
