<?php

declare(strict_types=1);

namespace Cvek\Kinesis\Messenger\Transport;

use Aws\Kinesis\KinesisClient;
use Cvek\Kinesis\Messenger\Receiver\KinesisReceiverProperties;
use Cvek\Kinesis\Messenger\Sender\KinesisSenderProperties;
use Cvek\Kinesis\Messenger\Receiver\KinesisReceiver;
use Cvek\Kinesis\Messenger\Sender\KinesisSender;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

class KinesisTransport implements TransportInterface
{
    private LoggerInterface $logger;
    private SerializerInterface $serializer;
    private KinesisClient $kinesisClient;
    private KinesisSenderProperties $kinesisSenderProperties;
    private KinesisReceiverProperties $kinesisReceiverProperties;
    private KinesisSender $sender;
    private KinesisReceiver $receiver;

    public function __construct(
        LoggerInterface           $logger,
        SerializerInterface       $serializer,
        KinesisClient             $kinesisClient,
        KinesisSenderProperties   $kafkaSenderProperties,
        KinesisReceiverProperties $kafkaReceiverProperties
    ) {
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->kinesisClient = $kinesisClient;
        $this->kinesisSenderProperties = $kafkaSenderProperties;
        $this->kinesisReceiverProperties = $kafkaReceiverProperties;
    }

    public function get(): iterable
    {
        return $this->getReceiver()->get();
    }

    public function ack(Envelope $envelope): void
    {
        $this->getReceiver()->ack($envelope);
    }

    public function reject(Envelope $envelope): void
    {
        $this->getReceiver()->reject($envelope);
    }

    public function send(Envelope $envelope): Envelope
    {
        return $this->getSender()->send($envelope);
    }

    private function getSender(): KinesisSender
    {
        return $this->sender ?? $this->sender = new KinesisSender(
            $this->logger,
            $this->serializer,
            $this->kinesisClient,
            $this->kinesisSenderProperties
        );
    }

    private function getReceiver(): KinesisReceiver
    {
        return $this->receiver ?? $this->receiver = new KinesisReceiver(
            $this->logger,
            $this->serializer,
            $this->kinesisClient,
            $this->kinesisReceiverProperties
        );
    }
}
