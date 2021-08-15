<?php

namespace App\DataFixtures;

use App\Entity\Substance;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SubstanceFixtures extends Fixture
{
    const REFERENCE = 'substance';

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

		$substance = new Substance();
		$substance->setName('Test Substance');

		$manager->persist($substance);

		$this->addReference(self::REFERENCE . '_0', $substance);

        for ($i = 1; $i < 5; $i++) {
            $substance = new Substance();
            $substance->setName(trim($faker->sentence(2), '.'));
            $manager->persist($substance);

            $this->addReference(self::REFERENCE . '_' . $i, $substance);
        }

        $manager->flush();
    }
}
