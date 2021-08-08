<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ApiRequester
{
    private HttpClientInterface $httpClient;
    private string $apiUrl;
    private SessionInterface $session;

    public function __construct(HttpClientInterface $httpClient, SessionInterface $session, string $apiUrl)
    {
        $this->httpClient = $httpClient;
        $this->apiUrl = trim($apiUrl, '/');
        $this->session = $session;
    }

    public function request(string $method, string $uri, $body = null)
    {
        $response = $this->httpClient->request(
            strtoupper($method),
            $this->apiUrl . $uri,
            [
                'headers' => [
                    'Content-Type' => 'application/ld+json',
                    'Authorization' => 'Bearer ' . $this->session->get('token'),
                ],
                'body' => $body
                    ? (is_array($body)
                        ? json_encode($body)
                        : $body)
                    : '',
            ]
        );

        $this->processError($response);

        return $response;
    }

    private function processError(ResponseInterface $response)
    {
        $statusCode = $response->getStatusCode();
        if ($statusCode == 200) {
            return;
        }

        $content = json_decode($response->getContent(false), true);

        $message = 'Произошла ошибка запроса к API';
        if (is_array($content)) {
            if (array_key_exists('hydra:description', $content)) {
                $message = $content['hydra:description'];
            } elseif (array_key_exists('message', $content)) {
                $message = $content['message'];
            }
        }

        switch ($statusCode) {
            case 400:
                throw new BadRequestException($message);
            case 401:
                throw new AuthenticationException($message, 401);
            case 404:
                throw new NotFoundHttpException($message);
            case 422:
                throw new \Exception($message, 422);
        }
    }
}