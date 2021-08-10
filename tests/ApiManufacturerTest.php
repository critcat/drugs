<?php

namespace App\Tests;

use App\Service\ApiRequester;

class ApiManufacturerTest extends ApiBaseTest
{
    public function testGetManufacturers()
    {
        $client = $this->setupClient(
            '{"hydra:member": [{"@id":"\/api\/manufacturers\/1","@type":"Manufacturer","name":"Manufacturer 1","site":"https:\/\/foo.com"},{"@id":"\/api\/manufacturers\/2","@type":"Manufacturer","name":"Manufacturer 2","site":"https:\/\/boo.com"},{"@id":"\/api\/manufacturers\/3","@type":"Manufacturer","name":"Manufacturer 3","site":"https:\/\/baz.com"}]}',
            ['response_headers' => ['HTTP/1.1 200 OK']]
        );

        $requester = new ApiRequester($client, $this->session, $this->apiUri);

        $result = $requester->getManufacturers();

        $this->assertIsArray($result);
        $this->assertContains('/api/manufacturers/1', $result[0]);
        $this->assertEquals('https://foo.com', $result[0]['site']);
    }
}