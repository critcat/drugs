<?php

namespace App\Controller\Api;

use App\Service\Api\ManufacturerHandler;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/manufacturers")
 */
class ManufacturerController extends AbstractController
{
	protected ManufacturerHandler $handler;

	public function __construct(ManufacturerHandler $handler)
	{
		$this->handler = $handler;
	}

	/**
	 * @Route(methods={"GET"})
	 * @OA\Get(
	 *     path="/api/manufacturers",
	 *     tags={"manufacturer"},
	 *     summary="Показать список производителей",
	 *     operationId="getManufacturersList",
	 *     @OA\Response(
	 *         response=200,
	 *         description="Список производителей",
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(ref="#/components/schemas/Manufacturer"),
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=404,
	 *         description="Resource not found"
	 *     )
	 * )
	 */
	public function list(): JsonResponse
	{
		$manufacturers = $this->handler->getAll();

		return $this->json(
			['data' => $manufacturers],
			200,
			[],
			['groups' => ['manufacturer:read']]
		);
	}
}