<?php

namespace App\Service\Api;

use App\Entity\Drug;
use App\Entity\Manufacturer;
use App\Entity\Substance;
use Symfony\Component\HttpFoundation\Request;

class DrugHandler extends BaseHandler
{
	public function getAll(): array
	{
		return $this->em->getRepository(Drug::class)->findAll();
	}

	public function getOne(int $id): ?Drug
	{
		return $this->em->getRepository(Drug::class)->find($id);
	}

	public function post(Request $request)
	{
		try {
			if (!$data = $this->convertRequestData($request)) {
				$this->setError(self::ERROR_CODE_INVALID_INPUT, self::ERROR_MESSAGE_INVALID_INPUT);

				return false;
			}

			$drug = new Drug();
			$drug = $this->hydrateDrug($drug, $data);

			$errors = $this->validate($drug);
			if (count($errors)) {
				$this->setError(self::ERROR_CODE_INVALID_INPUT, $errors);

				return false;
			}

			$this->em->persist($drug);
			$this->em->flush();

			return $drug;
		} catch (\Exception $e) {
			$this->setError(self::ERROR_CODE_ERROR, self::ERROR_MESSAGE_ERROR);

			return false;
		}
	}

	public function put(int $id, Request $request)
	{
		try {
			if (!$data = $this->convertRequestData($request)) {
				$this->setError(self::ERROR_CODE_INVALID_INPUT, self::ERROR_MESSAGE_INVALID_INPUT);

				return false;
			}

			if (!$drug = $this->getOne($id)) {
				$this->setError(self::ERROR_CODE_NOT_FOUND, self::ERROR_MESSAGE_NOT_FOUND);

				return false;
			}

			$drug = $this->hydrateDrug($drug, $data);

			$errors = $this->validate($drug);
			if (count($errors)) {
				$this->setError(self::ERROR_CODE_INVALID_INPUT, $errors);

				return false;
			}

			$this->em->flush();

			return $drug;
		} catch (\Exception $e) {
			$this->setError(self::ERROR_CODE_ERROR, self::ERROR_MESSAGE_ERROR);

			return false;
		}
	}

	public function delete(int $id): bool
	{
		try {
			if (!$drug = $this->getOne($id)) {
				$this->setError(self::ERROR_CODE_NOT_FOUND, self::ERROR_MESSAGE_NOT_FOUND);

				return false;
			}

			$this->em->remove($drug);
			$this->em->flush();

			return true;
		} catch (\Exception $e) {
			$this->setError(self::ERROR_CODE_ERROR, self::ERROR_MESSAGE_ERROR);

			return false;
		}
	}

	private function hydrateDrug(Drug $drug, array $data): Drug
	{
		if (array_key_exists('name', $data) && $data['name']) {
			$drug->setName($data['name']);
		}

		if (array_key_exists('price', $data) && $data['price']) {
			$drug->setPrice(floatval($data['price']));
		}

		if (array_key_exists('manufacturer', $data) && $data['manufacturer']) {
			$manufacturer = $this->em->getRepository(Manufacturer::class)->find($data['manufacturer']);
			$drug->setManufacturer($manufacturer);
		}

		if (array_key_exists('substance', $data) && $data['substance']) {
			$substance = $this->em->getRepository(Substance::class)->find($data['substance']);
			$drug->setSubstance($substance);
		}

		return $drug;
	}
}