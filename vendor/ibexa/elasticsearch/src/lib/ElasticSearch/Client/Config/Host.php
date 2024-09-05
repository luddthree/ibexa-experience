<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\Client\Config;

use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

final class Host
{
    public const DEFAULT_PORT = 9200;
    public const DEFAULT_HOST = 'localhost';
    public const DEFAULT_SCHEME = 'http';

    /** @var string */
    private $host;

    /** @var int */
    private $port;

    /** @var string */
    private $scheme;

    /** @var string|null */
    private $path;

    /** @var string|null */
    private $user;

    /** @var string|null */
    private $pass;

    public function __construct(
        string $host = self::DEFAULT_HOST,
        int $port = self::DEFAULT_PORT,
        string $scheme = self::DEFAULT_SCHEME,
        ?string $path = null,
        ?string $user = null,
        ?string $pass = null
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->scheme = $scheme;
        $this->path = $path;
        $this->user = $user;
        $this->pass = $pass;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function getPass(): ?string
    {
        return $this->pass;
    }

    public static function fromString(string $dsn): self
    {
        $properties = parse_url($dsn);

        if ($properties === false) {
            throw new InvalidArgumentException('$url', 'Unable to parse dsn: ' . $dsn);
        }

        return self::fromArray($properties);
    }

    public static function fromArray(array $properties): self
    {
        return new Host(
            $properties['host'] ?? self::DEFAULT_HOST,
            $properties['port'] ?? self::DEFAULT_PORT,
            $properties['scheme'] ?? self::DEFAULT_SCHEME,
            $properties['path'] ?? null,
            $properties['user'] ?? null,
            $properties['pass'] ?? null
        );
    }
}

class_alias(Host::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\Client\Config\Host');
