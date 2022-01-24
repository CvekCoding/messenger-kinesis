<?php

declare(strict_types=1);

namespace Cvek\Kinesis\Messenger\Sender;

final class KinesisSenderProperties
{
    private string $topicName;

    private int $flushRetries;

    public function __construct(string $topicName, int $flushRetries)
    {
        $this->topicName = $topicName;
        $this->flushRetries = $flushRetries;
    }

    public function getStreamName(): string
    {
        return $this->topicName;
    }

    public function getFlushRetries(): int
    {
        return $this->flushRetries;
    }
}
