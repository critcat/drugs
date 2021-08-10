<?php

namespace App\Tests;

use App\Service\ApiRequester;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class ApiRequesterExceptionTest extends ApiBaseTest
{
    public function testTokenNotFound()
    {
        $client = $this->setupClient(
            '{"code":401,"message":"JWT Token not found"}',
            ['response_headers' => ['HTTP/1.1 401 Unauthorized']]
        );

        $requester = new ApiRequester($client, $this->session, $this->apiUri);

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('JWT Token not found');
        $requester->getManufacturers();
    }

    public function testTokenNotExpired()
    {
        $client = $this->setupClient(
            '{"code":401,"message":"Expired JWT Token"}',
            ['response_headers' => ['HTTP/1.1 401 Unauthorized']]
        );

        $this->session->set('token', 'token_value');

        $requester = new ApiRequester($client, $this->session, $this->apiUri);

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Expired JWT Token');
        $requester->getManufacturers();
    }

    public function testBadRequest()
    {
        $client = $this->setupClient(
            '{"hydra:description": "Bad Request"}',
            ['response_headers' => ['HTTP/1.1 400 Bad Request']]
        );

        $this->session->set('token', 'token_value');

        $requester = new ApiRequester($client, $this->session, $this->apiUri);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Bad Request');
        $requester->getManufacturers();
    }

    public function testNotFoundWithMessage()
    {
        $client = $this->setupClient(
            '{"hydra:description": "Not Found"}',
            ['response_headers' => ['HTTP/1.1 404 Not Found']]
        );

        $this->session->set('token', 'token_value');

        $requester = new ApiRequester($client, $this->session, $this->apiUri);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Not Found');
        $requester->getManufacturers();
    }

    public function testNotFoundWithoutMessage()
    {
        $client = $this->setupClient(
            '',
            ['response_headers' => ['HTTP/1.1 404 Not Found']]
        );

        $this->session->set('token', 'token_value');

        $requester = new ApiRequester($client, $this->session, $this->apiUri);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Произошла ошибка запроса к API');
        $requester->getManufacturers();
    }

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