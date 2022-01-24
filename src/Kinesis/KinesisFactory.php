<?php

declare(strict_types=1);

namespace Cvek\Kinesis\Kinesis;

use Aws\Kinesis\KinesisClient;

class KinesisFactory
{
    private KinesisClient $client;

    public function __construct(string $key, string $secret)
    {
        $this->client = new KinesisClient(['credentials' => ['key'=> $key, 'secret' => $secret]]);
    }

    public function getClient(): KinesisClient
    {
        return $this->client;
    }
}
