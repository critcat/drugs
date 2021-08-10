<?php

namespace App\Tests;

use App\Service\ApiRequester;
use Symfony\Component\HttpFoundation\Request;

class ApiDrugTest extends ApiBaseTest
{
    public function testGetDrugs()
    {
        $client = $this->setupClient(
            '{"hydra:member": [
            {
                "@id": "/api/drugs/1",
                "@type": "Drug",
                "name": "Фуфломицин",
                "substance": {
                    "@id": "/api/substances/3",
                    "@type": "Substance",
                    "name": "Minima sapiente"
                },
                "manufacturer": {
                    "@id": "/api/manufacturers/3",
                    "@type": "Manufacturer",
                    "name": "Considine-Schneider",
                    "site": "https://carter.com"
                },
                "price": 3.15
            },
            {
                "@id": "/api/drugs/2",
                "@type": "Drug",
                "name": "Nam",
                "substance": {
                    "@id": "/api/substances/1",
                    "@type": "Substance",
                    "name": "Vel voluptas temporibus"
                },
                "manufacturer": {
                    "@id": "/api/manufacturers/4",
                    "@type": "Manufacturer",
                    "name": "Kutch-Heaney",
                    "site": "https://schuppe.net"
                },
                "price": 792.81
            }
        ]}',
            ['response_headers' => ['HTTP/1.1 200 OK']]
        );

        $requester = new ApiRequester($client, $this->session, $this->apiUri);

        $result = $requester->getDrugs();

        $this->assertIsArray($result);
        $this->assertContains('/api/substances/1', $result[1]['substance']);
        $this->assertEquals('/api/manufacturers/3', $result[0]['manufacturer']['@id']);

        $this->assertEquals('Фуфломицин', $result[0]['name']);
        $this->assertIsFloat($result[0]['price']);
        $this->assertEquals(3.15, $result[0]['price']);
    }

    public function testGetDrug()
    {
        $drugId = 5;
        $client = $this->setupClient(
            '{
                "@id": "/api/drugs/' . $drugId . '",
                "@type": "Drug",
                "name": "Фуфломицин",
                "substance": {
                    "@id": "/api/substances/3",
                    "@type": "Substance",
                    "name": "Minima sapiente"
                },
                "manufacturer": {
                    "@id": "/api/manufacturers/3",
                    "@type": "Manufacturer",
                    "name": "Considine-Schneider",
                    "site": "https://carter.com"
                },
                "price": 3.15
            }',
            ['response_headers' => ['HTTP/1.1 200 OK']]
        );

        $requester = new ApiRequester($client, $this->session, $this->apiUri);

        $result = $requester->getDrug($drugId);

        $this->assertIsArray($result);

        $this->assertEquals('/api/drugs/' . $drugId, $result['@id']);

        $this->assertContains('/api/substances/3', $result['substance']);
        $this->assertEquals('/api/manufacturers/3', $result['manufacturer']['@id']);

        $this->assertEquals('Фуфломицин', $result['name']);
        $this->assertIsFloat($result['price']);
        $this->assertEquals(3.15, $result['price']);
    }

    public function testInsertDrugInValid()
    {
        $client = $this->setupClient(
            '{}',
            ['response_headers' => ['HTTP/1.1 200 OK']]
        );

        $requester = new ApiRequester($client, $this->session, $this->apiUri);

        $request = new Request();
        $request->request->add([
            'name' => 'Фуфломицин',
            'price' => 3.15,
            'substance' => '/api/substances/5',
            'manufacturer' => '/api/manufacturers/3',
        ]);

        $this->expectExceptionCode(500);

        $requester->insertDrug($request);
    }

    public function testInsertDrugValid()
    {
        $client = $this->setupClient(
            '{}',
            ['response_headers' => ['HTTP/1.1 201 Created']]
        );

        $requester = new ApiRequester($client, $this->session, $this->apiUri);

        $request = new Request();
        $request->request->add([
            'name' => 'Фуфломицин',
            'price' => 3.15,
            'substance' => '/api/substances/5',
            'manufacturer' => '/api/manufacturers/3',
        ]);

        $this->assertNull($requester->insertDrug($request));
    }

    public function testDeleteDrugInvalid()
    {
        $client = $this->setupClient(
            '{}',
            ['response_headers' => ['HTTP/1.1 200 OK']]
        );

        $requester = new ApiRequester($client, $this->session, $this->apiUri);

        $this->expectExceptionCode(500);

        $requester->deleteDrug(100500);
    }

    public function testDeleteDrugValid()
    {
        $client = $this->setupClient(
            '{}',
            ['response_headers' => ['HTTP/1.1 204 No Content']]
        );

        $requester = new ApiRequester($client, $this->session, $this->apiUri);

        $this->assertNull($requester->deleteDrug(100500));
    }
}