<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\Client\Config;

final class SSLCACert
{
    /** @var string */
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public static function fromArray(array $properties): self
    {
        return new self(
            $properties['path'],
        );
    }
}

class_alias(SSLCACert::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\Client\Config\SSLCACert');
