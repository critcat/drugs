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
        $drugs = $this->apiRequester->getDrugs();

        return $this->render('drug/list.html.twig', [
            'drugs' => $drugs,
        ]);
    }

    /**
     * @Route("/new", name="drug_new", methods={"GET"})
     */
    public function new()
    {
        $data = $this->apiRequester->getDataForDrugInsertion();

        return $this->render('drug/insert.html.twig', [
            'data' => $data,
        ]);
    }

    /**
     * @Route("/new", name="drug_insert", methods={"POST"})
     */
    public function insert(Request $request)
    {
        $this->apiRequester->insertDrug($request);

        return $this->redirectToRoute('drug_list');
    }

    /**
     * @Route("/delete/{id}", name="drug_delete")
     */
    public function delete($id)
    {
        $this->apiRequester->deleteDrug($id);
        $this->addFlash('success', 'Запись успешно удалена');

        return $this->redirectToRoute('drug_list');
    }

    /**
     * @Route("/{id}", name="drug_edit", methods={"GET"})
     */
    public function edit($id)
    {
        $data = $this->apiRequester->getDataForDrugUpdate($id);

        return $this->render('drug/edit.html.twig', [
            'id' => $id,
            'data' => $data,
        ]);
    }

    /**
     * @Route("/{id}", name="drug_update", methods={"POST"})
     */
    public function update($id, Request $request)
    {
        $this->apiRequester->updateDrug($id, $request);

        return $this->redirectToRoute('drug_list');
    }
}
