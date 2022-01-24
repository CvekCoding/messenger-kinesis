<?php
/*
 * This file is part of the Aqua Delivery package.
 *
 * (c) Sergey Logachev <svlogachev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cvek\Kinesis\Messenger\Transport;

final class Dsn
{
    private string $host;
    private string $key;
    private string $secret;
    private string $region;
    private string $version;

    public function __construct(string $host, string $key, string $secret, $region, $version)
    {
        $this->host = $host;
        $this->key = $key;
        $this->secret = $secret;
        $this->region = $region;
        $this->version = $version;
    }

    public static function fromString(string $dsn, array $options): self
    {
        if (false === $parsedDsn = \parse_url($dsn)) {
            throw new \InvalidArgumentException(sprintf('The "%s" mailer DSN is invalid.', $dsn));
        }

        if (!isset($parsedDsn['host'])) {
            throw new \InvalidArgumentException(sprintf('The "%s" mailer DSN must contain a host (use "default" by default).', $dsn));
        }

        $key = $options['key'] ?? '' !== ($parsedDsn['user'] ?? '') ? urldecode($parsedDsn['user']) : null;
        $secret = $options['secret'] ??  '' !== ($parsedDsn['pass'] ?? '') ? urldecode($parsedDsn['pass']) : null;

        parse_str($parsedDsn['query'] ?? '', $query);
        $region = $options['region'] ?? $query['region'] ?? '';
        $version = $options['version'] ?? $query['version'] ?? '';

        return new self($parsedDsn['scheme'], $parsedDsn['host'], $key, $secret, $region, $version);
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function getVersion(): string
    {
        return $this->version;
    }
}
