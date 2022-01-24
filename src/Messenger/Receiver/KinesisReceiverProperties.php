<?php

declare(strict_types=1);

namespace Cvek\Kinesis\Messenger\Receiver;

final class KinesisReceiverProperties
{
    private string $topicName;

    private int $receiveTimeoutMs;

    private bool $commitAsync;

    public function __construct(
        string $topicName,
        int $receiveTimeoutMs,
        bool $commitAsync
    ) {
        $this->topicName = $topicName;
        $this->receiveTimeoutMs = $receiveTimeoutMs;
        $this->commitAsync = $commitAsync;
    }

    public function getTopicName(): string
    {
        return $this->topicName;
    }

    public function getReceiveTimeoutMs(): int
    {
        return $this->receiveTimeoutMs;
    }

    public function isCommitAsync(): bool
    {
        return $this->commitAsync;
    }
}
