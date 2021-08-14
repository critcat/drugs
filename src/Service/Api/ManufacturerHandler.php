<?php

namespace App\Service\Api;

use App\Entity\Manufacturer;

class ManufacturerHandler extends BaseHandler
{
	public function getAll(): array
	{
		return $this->em->getRepository(Manufacturer::class)->findAll();
	}
}