<?php

namespace App\Service\Api;

use App\Entity\Substance;

class SubstanceHandler extends BaseHandler
{
	public function getAll(): array
	{
		return $this->em->getRepository(Substance::class)->findAll();
	}
}