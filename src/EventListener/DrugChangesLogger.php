<?php

namespace App\EventListener;

use App\Entity\Drug;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class DrugChangesLogger
{
    private $logger;
    private $serializer;

    public function __construct(LoggerInterface $drugsChangesLogger, SerializerInterface $serializer)
    {
        $this->logger = $drugsChangesLogger;
        $this->serializer = $serializer;
    }

    public function postUpdate(Drug $drug): void
    {
        $this->logger->info('Drug was updated', [
            'drug' => $this->serializer->serialize($drug, 'json'),
        ]);
    }

    public function postPersist(Drug $drug): void
    {
        $this->logger->info('Drug was inserted', [
            'drug' => $this->serializer->serialize($drug, 'json'),
        ]);
    }

    public function preRemove(Drug $drug): void
    {
        $this->logger->info('Drug was deleted', [
            'drug' => $this->serializer->serialize($drug, 'json'),
        ]);
    }
}