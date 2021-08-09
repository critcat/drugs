<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
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

    public function getManufacturers(): array
    {
        $response = $this->request('GET', '/api/manufacturers');

        return $response->toArray()['hydra:member'];
    }

    public function getSubstances(): array
    {
        $response = $this->request('GET', '/api/substances');

        return $response->toArray()['hydra:member'];
    }

    public function getDrugs(): array
    {
        $response = $this->request('GET', '/api/drugs');

        return $response->toArray()['hydra:member'];
    }

    public function getDrug(int $id): array
    {
        $response = $this->request('GET','/api/drugs/' . $id);

        return $response->toArray();
    }

    public function getDataForDrugInsertion() : array
    {
        return [
            'manufacturers' => $this->getManufacturers(),
            'substances' => $this->getSubstances(),
        ];
    }

    public function insertDrug(Request $request): void
    {
        $body = [
            'name' => $request->request->get('name'),
            'price' => floatval($request->request->get('price')),
            'manufacturer' => $request->request->get('manufacturer'),
            'substance' => $request->request->get('substance'),
        ];

        $this->request(
            'POST',
            '/api/drugs',
            json_encode($body)
        );
    }

    public function getDataForDrugUpdate(int $id): array
    {
        return [
            'drug' => $this->getDrug($id),
            'manufacturers' => $this->getManufacturers(),
            'substances' => $this->getSubstances(),
        ];
    }

    public function updateDrug(int $id, Request $request): void
    {
        $body = [
            'name' => $request->request->get('name'),
            'price' => floatval($request->request->get('price')),
            'manufacturer' => $request->request->get('manufacturer'),
            'substance' => $request->request->get('substance'),
        ];

        $this->request(
            'PUT',
            '/api/drugs/' . $id,
            json_encode($body)
        );
    }

    public function deleteDrug(int $id): void
    {
        $this->request(
            'DELETE',
            '/api/drugs/' . $id
        );
    }

    public function login(string $username, string $password)
    {
        $response = $this->request(
            'POST',
            '/api/login_check',
            json_encode([
                'username' => $username,
                'password' => $password,
            ])
        );

        return $response->toArray()['token'];
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