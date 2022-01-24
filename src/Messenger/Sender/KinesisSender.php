<?php

declare(strict_types=1);

namespace Cvek\Kinesis\Messenger\Sender;

use Aws\Exception\AwsException;
use Aws\Kinesis\KinesisClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Sender\SenderInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class KinesisSender implements SenderInterface
{
    private LoggerInterface $logger;
    private SerializerInterface $serializer;
    private KinesisClient $kinesisClient;
    private KinesisSenderProperties $properties;

    public function __construct(
        LoggerInterface $logger,
        SerializerInterface $serializer,
        KinesisClient $kinesisClient,
        KinesisSenderProperties $properties
    ) {
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->kinesisClient = $kinesisClient;
        $this->properties = $properties;
    }

    public function send(Envelope $envelope): Envelope
    {
        $payload = $this->serializer->encode($envelope);

        try {
            $this->kinesisClient->putRecord([
                'StreamName' => $this->properties->getStreamName(),
                'Data' => $payload['body'],
                'PartitionKey' => $payload['body'],
            ]);
        } catch (AwsException $e) {
            $this->logger->error($e->getAwsErrorMessage());
            throw $e;
        }

        $this->logger->info('Message was sent successfully');

        return $envelope;
    }
}
