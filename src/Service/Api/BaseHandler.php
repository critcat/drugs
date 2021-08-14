<?php

namespace App\Service\Api;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseHandler
{
	const ERROR_CODE_INVALID_INPUT = 400;
	const ERROR_CODE_NOT_FOUND = 404;
	const ERROR_CODE_ERROR = 500;
	const ERROR_MESSAGE_INVALID_INPUT = 'Invalid input';
	const ERROR_MESSAGE_NOT_FOUND = 'Resource not found';
	const ERROR_MESSAGE_ERROR = 'Error';

	protected EntityManagerInterface $em;
	protected int $errorCode;
	protected $errorMessage;
	protected ValidatorInterface $validator;

	public function __construct(EntityManagerInterface $em, ValidatorInterface $validator)
	{
		$this->em = $em;
		$this->validator = $validator;
	}

	protected function convertRequestData(Request $request): array
	{
		$data = json_decode($request->getContent(), true);
		if (!is_array($data)) {
			$data = [];
		}

		return $data;
	}

	protected function setError(int $code, $message = null)
	{
		$this->errorCode = $code;
		$this->errorMessage = $message;
	}

	public function getErrorCode(): int
	{
		return $this->errorCode;
	}

	public function getErrorMessage()
	{
		return $this->errorMessage;
	}

	protected function validate($entity): array
	{
		$errors = $this->validator->validate($entity);
		$errorMessages = [];

		if (count($errors)) {
			foreach ($errors as $error) {
				$errorMessages[] = $error->getMessage();
			}
		}

		return $errorMessages;
	}
}