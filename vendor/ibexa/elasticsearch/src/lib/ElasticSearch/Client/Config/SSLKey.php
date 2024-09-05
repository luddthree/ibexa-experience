<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\Client\Config;

final class SSLKey
{
    /** @var string */
    private $path;

    /** @var string|null */
    private $pass;

    public function __construct(string $path, ?string $pass = null)
    {
        $this->path = $path;
        $this->pass = $pass;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getPass(): ?string
    {
        return $this->pass;
    }

    public static function fromArray(array $properties): self
    {
        return new self(
            $properties['path'],
            $properties['pass'] ?? null
        );
    }
}

class_alias(SSLKey::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\Client\Config\SSLKey');
