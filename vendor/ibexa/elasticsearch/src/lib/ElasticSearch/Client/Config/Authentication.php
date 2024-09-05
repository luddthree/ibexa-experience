<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\Client\Config;

final class Authentication
{
    public const TYPE_BASIC = 'basic';
    public const TYPE_API_KEY = 'api_key';

    /** @var string */
    private $type;

    /** @var string[] */
    private $credentials;

    public function __construct(string $type, array $credentials)
    {
        $this->type = $type;
        $this->credentials = $credentials;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCredentials(): array
    {
        return $this->credentials;
    }

    public static function fromArray(array $properties): self
    {
        return new Authentication(
            $properties['type'],
            $properties['credentials']
        );
    }
}

class_alias(Authentication::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\Client\Config\Authentication');
