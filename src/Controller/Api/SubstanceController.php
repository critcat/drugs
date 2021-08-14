<?php

namespace App\Controller\Api;

use App\Service\Api\SubstanceHandler;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/substances")
 */
class SubstanceController extends AbstractController
{
	protected SubstanceHandler $handler;

	public function __construct(SubstanceHandler $handler)
	{
		$this->handler = $handler;
	}

	/**
	 * @Route(methods={"GET"})
	 * @OA\Get(
	 *     path="/api/substances",
	 *     tags={"substances"},
	 *     summary="Показать список веществ",
	 *     operationId="getSubstancesList",
	 *     @OA\Response(
	 *         response=200,
	 *         description="Список веществ",
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(ref="#/components/schemas/Substance"),
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
		$substances = $this->handler->getAll();

		return $this->json(
			['data' => $substances],
			200,
			[],
			['groups' => ['substance:read']]
		);
	}
}