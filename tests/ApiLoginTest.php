<?php

namespace App\Tests;

use App\Service\ApiRequester;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class ApiLoginTest extends ApiBaseTest
{
    public function testInvalidLogin()
    {
        $client = $this->setupClient(
            '{"code":401,"message":"Invalid credentials"}',
            ['response_headers' => ['HTTP/1.1 401 Unauthorized']]
        );

        $requester = new ApiRequester($client, $this->session, $this->apiUri);

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Invalid credentials');
        $requester->login('user', 'wrong_password');
    }

    public function testValidLogin()
    {
        $client = $this->setupClient(
            '{"token": "token.body"}',
            ['response_headers' => ['HTTP/1.1 200 OK']]
        );

        $requester = new ApiRequester($client, $this->session, $this->apiUri);

        $token = $requester->login('user', 'pass');
        $this->assertEquals("token.body", $token);
    }
}