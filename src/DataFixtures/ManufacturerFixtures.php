<?php

namespace App\DataFixtures;

use App\Entity\Manufacturer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ManufacturerFixtures extends Fixture
{
    const REFERENCE = 'manufacturer';

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

		$manufacturer = new Manufacturer();
		$manufacturer->setName('Test Manufacturer');
		$manufacturer->setSite('https://test-manufacturer.com');

		$manager->persist($manufacturer);

		$this->addReference(self::REFERENCE . '_0', $manufacturer);

        for ($i = 1; $i < 5; $i++) {
            $manufacturer = new Manufacturer();
            $manufacturer->setName($faker->company);
            $manufacturer->setSite('https://' . $faker->domainName);

            $manager->persist($manufacturer);

            $this->addReference(self::REFERENCE . '_' . $i, $manufacturer);
        }

        $manager->flush();
    }
}