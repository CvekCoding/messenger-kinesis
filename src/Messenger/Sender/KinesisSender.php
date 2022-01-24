<?php

declare(strict_types=1);

namespace Cvek\Kinesis\Messenger\Sender;

use Aws\Kinesis\KinesisClient;
use Cvek\Kinesis\Kinesis\KinesisFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Sender\SenderInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class KinesisSender implements SenderInterface
{
    private LoggerInterface $logger;

    private SerializerInterface $serializer;

    private KinesisFactory $kinesisFactory;

    private KinesisSenderProperties $properties;

    private KinesisClient $producer;

    public function __construct(
        LoggerInterface $logger,
        SerializerInterface $serializer,
        KinesisFactory $kinesisFactory,
        KinesisSenderProperties $properties
    ) {
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->kinesisFactory = $kinesisFactory;
        $this->properties = $properties;
    }

    public function send(Envelope $envelope): Envelope
    {
        $client = $this->getClient();
//        $stream = $producer->createStream(['StreamName' => $this->properties->getTopicName(), 'ShardCount' => 1]);

        $payload = $this->serializer->encode($envelope);

        $client->putRecord([
            // StreamName is required
            'StreamName' => $this->properties->getStreamName(),
            'Data' => $payload['body'],
            // PartitionKey is required
            'PartitionKey' => '1',
        ]);

        return $envelope;
    }

    private function getClient(): KinesisClient
    {
        return $this->kinesisFactory->getClient();
//        return $this->producer ?? $this->producer = $this->kinesisFactory->createProducer($this->properties->());
    }
}
