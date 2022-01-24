<?php

declare(strict_types=1);

namespace Cvek\Kinesis\Kinesis;

use Aws\Kinesis\KinesisClient;
use Cvek\Kinesis\Messenger\Transport\Dsn;

class KinesisFactory
{
    private KinesisClient $client;

    public function __construct(Dsn $dsn)
    {
        $this->client = new KinesisClient([
            'endpoint' => "https://{$dsn->getHost()}",
            'version' => $dsn->getVersion(),
            'region' => $dsn->getRegion(),
            'credentials' => [
                'key'=> $dsn->getKey(),
                'secret' => $dsn->getSecret(),
            ],
        ]);

    }

    public function getClient(): KinesisClient
    {
        return $this->client;
    }
}
