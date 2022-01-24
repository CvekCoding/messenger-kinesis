<?php

declare(strict_types=1);

namespace Cvek\Kinesis\Messenger\Sender;

final class KinesisSenderProperties
{
    private string $topicName;

    private int $flushTimeoutMs;

    private int $flushRetries;

    public function __construct(
        string $topicName,
        int $flushTimeoutMs,
        int $flushRetries
    ) {
        $this->topicName = $topicName;
        $this->flushTimeoutMs = $flushTimeoutMs;
        $this->flushRetries = $flushRetries;
    }

    public function getStreamName(): string
    {
        return $this->topicName;
    }

    public function getFlushTimeoutMs(): int
    {
        return $this->flushTimeoutMs;
    }

    public function getFlushRetries(): int
    {
        return $this->flushRetries;
    }
}
