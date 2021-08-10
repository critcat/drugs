<?php

namespace App\Tests;

use App\Service\ApiRequester;

class ApiSubstanceTest extends ApiBaseTest
{
    public function testGetSubstances()
    {
        $client = $this->setupClient(
            '{"hydra:member": [{"@id":"\/api\/substances\/2","@type":"Substance","name":"Illum et atque"},{"@id":"\/api\/substances\/3","@type":"Substance","name":"Minima sapiente"}]}',
            ['response_headers' => ['HTTP/1.1 200 OK']]
        );

        $requester = new ApiRequester($client, $this->session, $this->apiUri);

        $result = $requester->getManufacturers();

        $this->assertIsArray($result);
        $this->assertContains('/api/substances/2', $result[0]);
        $this->assertEquals('Minima sapiente', $result[1]['name']);
    }
}