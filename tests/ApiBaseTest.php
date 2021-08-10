<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * Базовый класс, от которого наследуются другие тесты для ApiRequester
 * Нужен для того, чтобы не дублировать код настроек
 */
class ApiBaseTest extends TestCase
{
    protected $session;
    protected $apiUri;

    protected function setUp(): void
    {
        $this->session = new Session(new MockArraySessionStorage());
        $this->apiUri = 'http://127.0.0.1:5000';
    }

    protected function setupClient(string $body, array $headers): MockHttpClient
    {
        return new MockHttpClient([
            new MockResponse($body, $headers)
        ]);
    }

    /**
     * Тест добавлен чтобы PHPUnit не ругался на то, что файл без тестов
     */
    public function testTrue()
    {
        $this->assertTrue(true);
    }
}