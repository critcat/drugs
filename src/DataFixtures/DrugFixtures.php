<?php

namespace App\DataFixtures;

use App\Entity\Drug;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class DrugFixtures extends Fixture implements DependentFixtureInterface
{
    private array $referencesIndex = [];
    private $faker;

    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();

		$drug = new Drug();
		$drug->setName('Test Drug')
			->setPrice(123.45)
			->setManufacturer($this->getReference(ManufacturerFixtures::REFERENCE . '_0'))
			->setSubstance($this->getReference(SubstanceFixtures::REFERENCE . '_0'));

		$manager->persist($drug);

        for ($i = 1; $i < 10; $i++) {
            $drug = new Drug();
            $drug->setName(ucfirst($this->faker->word))
                ->setPrice($this->faker->randomFloat(2, 5, 1000))
                ->setManufacturer($this->getRandomReference(ManufacturerFixtures::REFERENCE))
                ->setSubstance($this->getRandomReference(SubstanceFixtures::REFERENCE));

            $manager->persist($drug);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SubstanceFixtures::class,
            ManufacturerFixtures::class,
        ];
    }

    private function getRandomReference(string $className): object
    {
        if (!isset($this->referencesIndex[$className])) {
            $this->referencesIndex[$className] = [];
            foreach ($this->referenceRepository->getReferences() as $key => $ref) {
                if (strpos($key, $className . '_') === 0) {
                    $this->referencesIndex[$className][] = $key;
                }
            }
        }

        if (empty($this->referencesIndex[$className])) {
            throw new \Exception(sprintf('Cannot find any references for class "%s"', $className));
        }

        $randomReferenceKey = $this->faker->randomElement($this->referencesIndex[$className]);

        return $this->getReference($randomReferenceKey);
    }
}