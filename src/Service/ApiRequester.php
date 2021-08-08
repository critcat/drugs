<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiRequester
{
    private HttpClientInterface $httpClient;
    private string $apiUrl;

    public function __construct(HttpClientInterface $httpClient, string $apiUrl)
    {
        $this->httpClient = $httpClient;
        $this->apiUrl = trim($apiUrl, '/');
    }

    public function request(string $method, string $uri, $body = null)
    {
        $response = $this->httpClient->request(
            strtoupper($method),
            $this->apiUrl . $uri,
            [
                'headers' => [
                    'Content-Type' => 'application/ld+json',
                ],
                'body' => $body
                    ? (is_array($body)
                        ? json_encode($body)
                        : $body)
                    : '',
            ]
        );

        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            $content = json_decode($response->getContent(false), true);
            dump($content);
            switch ($statusCode) {
                case 400:
                    throw new BadRequestException($content['hydra:description']);
                case 401:
                    throw new AuthenticationException($content['message']);
                case 404:
                    throw new NotFoundHttpException($content['hydra:description']);
                case 422:
                    throw new \Exception($content['hydra:description'], 422);
            }
        }

        return $response;
    }
}