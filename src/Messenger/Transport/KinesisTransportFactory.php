<?php

declare(strict_types=1);

namespace Cvek\Kinesis\Messenger\Transport;

use Cvek\Kinesis\Kinesis\KinesisFactory;
use Cvek\Kinesis\Messenger\Receiver\KinesisReceiverProperties;
use Cvek\Kinesis\Messenger\Sender\KinesisSenderProperties;
use function explode;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use function str_replace;
use function strpos;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

class KinesisTransportFactory implements TransportFactoryInterface
{
    private const DSN_PROTOCOLS = [
        self::DSN_PROTOCOL_KAFKA,
        self::DSN_PROTOCOL_KAFKA_SSL,
    ];
    private const DSN_PROTOCOL_KAFKA = 'kinesis://';
    private const DSN_PROTOCOL_KAFKA_SSL = 'kinesis+ssl://';

    private ?LoggerInterface $logger;

    public function __construct(?LoggerInterface $logger)
    {
        $this->logger = $logger ?? new NullLogger();
    }

    public function supports(string $dsn, array $options): bool
    {
        foreach (self::DSN_PROTOCOLS as $protocol) {
            if (0 === strpos($dsn, $protocol)) {
                return true;
            }
        }

        return false;
    }

    public function createTransport(string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        // Set a rebalance callback to log partition assignments (optional)
//        $conf->setRebalanceCb($this->createRebalanceCb($this->logger));

//        $brokers = $this->stripProtocol($dsn);
//        $conf->set('metadata.broker.list', implode(',', $brokers));

//        foreach (array_merge($options['topic_conf'] ?? [], $options['kafka_conf'] ?? []) as $option => $value) {
//            $conf->set($option, $value);
//        }

        $kinesisFactory = new KinesisFactory('', '');

        return new KinesisTransport(
            $this->logger,
            $serializer,
            $kinesisFactory,
            new KinesisSenderProperties(
                $options['topic']['name'],
                $options['flushTimeout'] ?? 10000,
                $options['flushRetries'] ?? 0
            ),
            new KinesisReceiverProperties(
                $options['topic']['name'],
                $options['receiveTimeout'] ?? 10000,
                $options['commitAsync'] ?? false
            )
        );
    }

    private function stripProtocol(string $dsn): array
    {
        $brokers = [];
        foreach (explode(',', $dsn) as $currentBroker) {
            foreach (self::DSN_PROTOCOLS as $protocol) {
                $currentBroker = str_replace($protocol, '', $currentBroker);
            }
            $brokers[] = $currentBroker;
        }

        return $brokers;
    }
}
