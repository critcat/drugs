<?php

namespace App\Controller\Api;

use App\Service\Api\DrugHandler;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/drugs")
 */
class DrugController extends AbstractController
{
    private DrugHandler $handler;

    public function __construct(DrugHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @Route(methods={"GET"})
	 * @OA\Get(
	 *     path="/api/drugs",
	 *     tags={"drug"},
	 *     summary="Получить список всех лекарств",
	 *     operationId="getDrugsList",
	 *     @OA\Response(
	 *         response=200,
	 *         description="Список лекарств",
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(ref="#/components/schemas/Drug"),
	 *         )
	 *     )
	 * )
     */
    public function list(): JsonResponse
    {
        $drugs = $this->handler->getAll();

        return $this->json(
            ['data' => $drugs],
            200,
            [],
            ['groups' => ['drugs:read']]
        );
    }

    /**
     * @Route("/{id}", methods={"GET"})
	 * @OA\Get(
	 *     path="/api/drugs/{id}",
	 *     tags={"drug"},
	 *     summary="Получить определенное лекарство",
	 *     operationId="getDrugById",
	 *     @OA\Parameter(
	 *         name="id",
	 *         in="path",
	 *         description="ID лекарственного препарата",
	 *         required=true,
	 *         @OA\Schema(
	 *             type="integer"
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Лекарство",
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(ref="#/components/schemas/Drug"),
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=404,
	 *         description="Resource not found"
	 *     )
	 * )
     */
    public function details($id): JsonResponse
    {
        if (!$drug = $this->handler->getOne($id)) {
			return $this->handleError(
				$this->handler::ERROR_CODE_NOT_FOUND,
				$this->handler::ERROR_MESSAGE_NOT_FOUND
			);
        }

        return $this->json(
            ['data' => $drug],
            200,
            [],
            ['groups' => ['drugs:read']]
        );
    }

    /**
     * @Route(methods={"POST"})
	 * @OA\Post(
	 *     path="/api/drugs",
	 *     tags={"drug"},
	 *     summary="Добавить новое лекарство",
	 *     operationId="insertDrug",
	 *     @OA\RequestBody(
	 *         description="Формат данных для добавления лекарства. Все поля обязательны",
	 *         required=true,
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(
	 *                 required={"name", "price", "manufacturer", "substance"},
	 *                 @OA\Property(
	 *                     property="name",
	 *                     description="Название добавляемого лекарства",
	 *                     type="string",
	 *                 ),
	 *                 @OA\Property(
	 *                     property="price",
	 *                     description="Название добавляемого лекарства",
	 *                     type="number",
	 *                     format="float",
	 *                 ),
	 *                 @OA\Property(
	 *                     property="manufacturer",
	 *                     description="ID производителя добавляемого лекарства",
	 *                     type="integer"
	 *                 ),
	 *                 @OA\Property(
	 *                     property="substance",
	 *                     description="ID производителя действующего вещества",
	 *                     type="integer"
	 *                 ),
     *	           ),
     *	       ),
	 *     ),
	 *     @OA\Response(
	 *         response=201,
	 *         description="Лекарство успешно добавлено",
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(ref="#/components/schemas/Drug"),
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=400,
	 *         description="Invalid input"
	 *     )
	 * )
     */
    public function insert(Request $request): JsonResponse
    {
    	if (false === ($drug = $this->handler->post($request))) {
			return $this->handleError();
		}

		return $this->json(
			['data' => $drug],
			201,
			[],
			['groups' => ['drugs:read']]
		);
    }

    /**
     * @Route("/{id}", methods={"PUT"})
	 * @OA\Put(
	 *     path="/api/drugs/{id}",
	 *     tags={"drug"},
	 *     summary="Изменить лекарство",
	 *     operationId="updateDrug",
	 *     @OA\Parameter(
	 *         name="id",
	 *         in="path",
	 *         description="ID редактируемого лекарства",
	 *         required=true,
	 *         @OA\Schema(
	 *             type="integer"
	 *         )
	 *     ),
	 *     @OA\RequestBody(
	 *         description="Формат данных для изменения лекарства. Допускается передача лишь некоторых параметров. Например, для изменения названия лекарства достаточно передать поле `name`, а для изменения цены и действующего вещества нужно передать два поля - `price` и `substance`",
	 *         required=true,
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(
	 *                 @OA\Property(
	 *                     property="name",
	 *                     description="Название добавляемого лекарства",
	 *                     type="string",
	 *                 ),
	 *                 @OA\Property(
	 *                     property="price",
	 *                     description="Название добавляемого лекарства",
	 *                     type="number",
	 *                     format="float",
	 *                 ),
	 *                 @OA\Property(
	 *                     property="manufacturer",
	 *                     description="ID производителя добавляемого лекарства",
	 *                     type="integer"
	 *                 ),
	 *                 @OA\Property(
	 *                     property="substance",
	 *                     description="ID производителя действующего вещества",
	 *                     type="integer"
	 *                 ),
	 *	           ),
	 *	       ),
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Лекарство успешно изменено",
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(ref="#/components/schemas/Drug"),
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=400,
	 *         description="Invalid input"
	 *     ),
	 *     @OA\Response(
	 *         response=404,
	 *         description="Resource not found"
	 *     )
	 * )
     */
    public function update(int $id, Request $request): JsonResponse
    {
    	if (false === ($drug = $this->handler->put($id, $request))) {
			return $this->handleError();
		}

		return $this->json(
			['data' => $drug],
			201,
			[],
			['groups' => ['drugs:read']]
		);
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
	 * @OA\Delete(
	 *     path="/api/drugs/{id}",
	 *     tags={"drug"},
	 *     summary="Удалить лекарство",
	 *     operationId="deleteDrug",
	 *     @OA\Parameter(
	 *         name="id",
	 *         in="path",
	 *         description="ID удаляемого лекарства",
	 *         required=true,
	 *         @OA\Schema(
	 *             type="integer"
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=204,
	 *         description="Лекарство успешно удалено",
	 *     ),
	 *     @OA\Response(
	 *         response=404,
	 *         description="Resource not found"
	 *     )
	 * )
     */
    public function delete(int $id): JsonResponse
    {
    	if (false === $this->handler->delete($id)) {
			return $this->handleError();
		}

        return $this->json(
            [],
            204
        );
    }

    protected function handleError(int $code = null, $message = null): JsonResponse
	{
		return $this->json(
			['message' => $message ?: $this->handler->getErrorMessage()],
			$code ?: $this->handler->getErrorCode()
		);
	}
}