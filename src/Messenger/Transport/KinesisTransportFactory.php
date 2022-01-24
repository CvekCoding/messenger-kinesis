<?php

declare(strict_types=1);

namespace Cvek\Kinesis\Messenger\Transport;

use Cvek\Kinesis\Kinesis\KinesisFactory;
use Cvek\Kinesis\Messenger\Receiver\KinesisReceiverProperties;
use Cvek\Kinesis\Messenger\Sender\KinesisSenderProperties;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use function strpos;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

class KinesisTransportFactory implements TransportFactoryInterface
{
    private const DSN_PROTOCOL = 'kinesis://';

    private ?LoggerInterface $logger;

    public function __construct(?LoggerInterface $logger)
    {
        $this->logger = $logger ?? new NullLogger();
    }

    public function supports(string $dsn, array $options): bool
    {
        return 0 === strpos($dsn, self::DSN_PROTOCOL);
    }

    public function createTransport(string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        $kinesisFactory = new KinesisFactory(Dsn::fromString($dsn, $options));

        return new KinesisTransport(
            $this->logger,
            $serializer,
            $kinesisFactory->getClient(),
            new KinesisSenderProperties(
                $options['stream']['name'],
                $options['flushRetries'] ?? 0
            ),
            new KinesisReceiverProperties(
                $options['stream']['name'],
                $options['receiveTimeout'] ?? 10000,
                $options['commitAsync'] ?? false
            )
        );
    }
}
