<?php

namespace App\Tests\Api;

use App\DataFixtures\DrugFixtures;
use App\DataFixtures\ManufacturerFixtures;
use App\DataFixtures\SubstanceFixtures;
use App\Entity\Drug;
use App\Entity\Manufacturer;
use App\Entity\Substance;
use App\Service\Api\DrugHandler;
use App\Service\Api\ManufacturerHandler;
use App\Service\Api\SubstanceHandler;
use Symfony\Component\HttpFoundation\Request;

class ApiTest extends FixtureAwareTestCase
{
	private $em;
	private $validator;

	protected function setUp(): void
	{
		parent::setUp();

		$kernel = self::bootKernel([
			'environment' => 'test',
			'debug' => false,
		]);

		DatabasePrimer::prime($kernel);

		$this->em = $kernel->getContainer()->get('doctrine')->getManager();
		$this->validator = $kernel->getContainer()->get('validator');
	}

	public function testManufacturersList()
	{
		$this->addFixture(new ManufacturerFixtures());
		$this->executeFixtures();

		$handler = new ManufacturerHandler($this->em, $this->validator);

		/** @var Manufacturer[] $manufacturers */
		$manufacturers = $handler->getAll();
		$testManufacturer = $manufacturers[0];

		$this->assertIsArray($manufacturers);
		$this->assertInstanceOf(Manufacturer::class, $testManufacturer);
		$this->assertEquals('Test Manufacturer', $testManufacturer->getName());
		$this->assertEquals('https://test-manufacturer.com', $testManufacturer->getSite());
	}

	public function testSubstancesList()
	{
		$this->addFixture(new SubstanceFixtures());
		$this->executeFixtures();

		$handler = new SubstanceHandler($this->em, $this->validator);

		/** @var Substance[] $substances */
		$substances = $handler->getAll();
		$testSubstance = $substances[0];

		$this->assertIsArray($substances);
		$this->assertInstanceOf(Substance::class, $testSubstance);
		$this->assertEquals('Test Substance', $testSubstance->getName());
	}

	public function testDrugsList()
	{
		$this->addFixture(new DrugFixtures());
		$this->executeFixtures();

		$handler = new DrugHandler($this->em, $this->validator);

		/** @var Drug[] $drugs */
		$drugs = $handler->getAll();
		$testDrug = $drugs[0];

		$this->assertIsArray($drugs);
		$this->assertInstanceOf(Drug::class, $testDrug);
		$this->assertEquals('Test Drug', $testDrug->getName());
		$this->assertEquals(123.45, $testDrug->getPrice());

		$this->assertInstanceOf(Manufacturer::class, $testDrug->getManufacturer());
		$this->assertEquals(1, $testDrug->getManufacturer()->getId());
		$this->assertInstanceOf(Substance::class, $testDrug->getSubstance());
		$this->assertEquals(1, $testDrug->getSubstance()->getId());
	}

	public function testGetOneDrug()
	{
		$this->addFixture(new DrugFixtures());
		$this->executeFixtures();

		$handler = new DrugHandler($this->em, $this->validator);

		/** @var Drug $drug */
		$drug = $handler->getOne(1);

		$this->assertInstanceOf(Drug::class, $drug);
		$this->assertEquals('Test Drug', $drug->getName());
		$this->assertEquals(123.45, $drug->getPrice());

		$this->assertInstanceOf(Manufacturer::class, $drug->getManufacturer());
		$this->assertEquals(1, $drug->getManufacturer()->getId());
		$this->assertInstanceOf(Substance::class, $drug->getSubstance());
		$this->assertEquals(1, $drug->getSubstance()->getId());
	}

	public function testNotFoundDrug()
	{
		$this->addFixture(new DrugFixtures());
		$this->executeFixtures();

		$handler = new DrugHandler($this->em, $this->validator);

		$result = $handler->getOne(20);

		$this->assertNull($result);
	}

	public function testInsertDrug()
	{
		$this->addFixture(new ManufacturerFixtures());
		$this->addFixture(new SubstanceFixtures());
		$this->executeFixtures();

		$handler = new DrugHandler($this->em, $this->validator);

		$request = $this->createMock(Request::class);
		$request->method('getContent')
			->willReturn('{"name": "Test Drug", "price": 45.67, "manufacturer": 1, "substance": 1}');

		/** @var Drug $drug */
		$drug = $handler->post($request);

		$this->assertInstanceOf(Drug::class, $drug);
		$this->assertEquals('Test Drug', $drug->getName());
		$this->assertEquals(45.67, $drug->getPrice());

		$this->assertInstanceOf(Manufacturer::class, $drug->getManufacturer());
		$this->assertEquals(1, $drug->getManufacturer()->getId());
		$this->assertEquals('Test Manufacturer', $drug->getManufacturer()->getName());
		$this->assertEquals('https://test-manufacturer.com', $drug->getManufacturer()->getSite());

		$this->assertInstanceOf(Substance::class, $drug->getSubstance());
		$this->assertEquals(1, $drug->getSubstance()->getId());
		$this->assertEquals('Test Substance', $drug->getSubstance()->getName());

		$this->assertInstanceOf(Drug::class, $drug->getManufacturer()->getDrugs()[0]);
	}

	public function testFailureInsertDrug()
	{
		$this->addFixture(new ManufacturerFixtures());
		$this->addFixture(new SubstanceFixtures());
		$this->executeFixtures();

		$handler = new DrugHandler($this->em, $this->validator);

		// не хватает параметров дял добавления записи
		$request = $this->createMock(Request::class);
		$request->method('getContent')
			->willReturn('{"name": "Test Drug", "price": 45}');
		$result = $handler->post($request);

		$this->assertFalse($result);
		$this->assertEquals(DrugHandler::ERROR_CODE_INVALID_INPUT, $handler->getErrorCode());
		$this->assertIsArray($handler->getErrorMessage());

		// неверно сформировано тело запроса - запятая вместо точки в цене лекарства
		$request = $this->createMock(Request::class);
		$request->method('getContent')
			->willReturn('{"name": "Test Drug", "price": 45,67 "manufacturer": 1, "substance": 1}');
		$result = $handler->post($request);

		$this->assertEquals(DrugHandler::ERROR_MESSAGE_INVALID_INPUT, $handler->getErrorMessage());
	}

	public function testFailureUpdateDrug()
	{
		$this->addFixture(new DrugFixtures());
		$this->executeFixtures();

		$handler = new DrugHandler($this->em, $this->validator);

		$request = $this->createMock(Request::class);
		$request->method('getContent')
			->willReturn('{"name": "Test Drug Updated", "price": 78,95, "manufacturer": 3, "substance": 2}');

		$result = $handler->put(3, $request);

		$this->assertFalse($result);

		$this->assertEquals(DrugHandler::ERROR_CODE_INVALID_INPUT, $handler->getErrorCode());
		$this->assertEquals(DrugHandler::ERROR_MESSAGE_INVALID_INPUT, $handler->getErrorMessage());
	}

	public function testNotFoundUpdateDrug()
	{
		$this->addFixture(new DrugFixtures());
		$this->executeFixtures();

		$handler = new DrugHandler($this->em, $this->validator);

		$request = $this->createMock(Request::class);
		$request->method('getContent')
			->willReturn('{"name": "Test Drug Updated", "price": 78.95, "manufacturer": 3, "substance": 2}');

		$result = $handler->put(30, $request);

		$this->assertFalse($result);

		$this->assertEquals(DrugHandler::ERROR_CODE_NOT_FOUND, $handler->getErrorCode());
		$this->assertEquals(DrugHandler::ERROR_MESSAGE_NOT_FOUND, $handler->getErrorMessage());
	}

	public function testFullUpdateDrug()
	{
		$this->addFixture(new DrugFixtures());
		$this->executeFixtures();

		$handler = new DrugHandler($this->em, $this->validator);

		$request = $this->createMock(Request::class);
		$request->method('getContent')
			->willReturn('{"name": "Test Drug Updated", "price": 78.95, "manufacturer": 3, "substance": 2}');

		$drug = $handler->put(3, $request);

		$this->assertInstanceOf(Drug::class, $drug);
		$this->assertEquals('Test Drug Updated', $drug->getName());
		$this->assertEquals(78.95, $drug->getPrice());

		$this->assertEquals(3, $drug->getManufacturer()->getId());
		$this->assertEquals(2, $drug->getSubstance()->getId());
	}

	public function testPartialUpdateDrug()
	{
		$this->addFixture(new DrugFixtures());
		$this->executeFixtures();

		$handler = new DrugHandler($this->em, $this->validator);

		// обновление только названия
		$request = $this->createMock(Request::class);
		$request->method('getContent')
			->willReturn('{"name": "Test Drug Partial Updated"}');

		$drug = $handler->put(1, $request);

		$this->assertInstanceOf(Drug::class, $drug);
		$this->assertEquals('Test Drug Partial Updated', $drug->getName());
		$this->assertEquals(123.45, $drug->getPrice());

		$this->assertEquals(1, $drug->getManufacturer()->getId());
		$this->assertEquals(1, $drug->getSubstance()->getId());

		// обновление только названия
		$request = $this->createMock(Request::class);
		$request->method('getContent')
			->willReturn('{"price": 456.78}');

		$drug = $handler->put(1, $request);

		$this->assertInstanceOf(Drug::class, $drug);
		$this->assertEquals('Test Drug Partial Updated', $drug->getName());
		$this->assertEquals(456.78, $drug->getPrice());

		$this->assertEquals(1, $drug->getManufacturer()->getId());
		$this->assertEquals(1, $drug->getSubstance()->getId());

		// обновление действующего вещества и производителя
		$request = $this->createMock(Request::class);
		$request->method('getContent')
			->willReturn('{"manufacturer": 4, "substance": 3}');

		$drug = $handler->put(1, $request);

		$this->assertInstanceOf(Drug::class, $drug);
		$this->assertEquals('Test Drug Partial Updated', $drug->getName());
		$this->assertEquals(456.78, $drug->getPrice());

		$this->assertEquals(4, $drug->getManufacturer()->getId());
		$this->assertEquals(3, $drug->getSubstance()->getId());
	}

	public function testNotFoundDeleteDrug()
	{
		$this->addFixture(new DrugFixtures());
		$this->executeFixtures();

		$handler = new DrugHandler($this->em, $this->validator);

		$result = $handler->delete(30);

		$this->assertFalse($result);

		$this->assertEquals(DrugHandler::ERROR_CODE_NOT_FOUND, $handler->getErrorCode());
		$this->assertEquals(DrugHandler::ERROR_MESSAGE_NOT_FOUND, $handler->getErrorMessage());
	}

	public function testDeleteDrug()
	{
		$this->addFixture(new DrugFixtures());
		$this->executeFixtures();

		$handler = new DrugHandler($this->em, $this->validator);

		$result = $handler->delete(1);

		$this->assertTrue($result);
	}
}