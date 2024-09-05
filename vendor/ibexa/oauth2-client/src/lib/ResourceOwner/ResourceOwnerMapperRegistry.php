<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\OAuth2Client\ResourceOwner;

use Ibexa\Contracts\OAuth2Client\Exception\ResourceOwner\MapperNotFoundException;
use Ibexa\Contracts\OAuth2Client\ResourceOwner\ResourceOwnerMapper;
use Ibexa\Contracts\OAuth2Client\ResourceOwner\ResourceOwnerMapperRegistry as ResourceOwnerMapperRegistryInterface;

final class ResourceOwnerMapperRegistry implements ResourceOwnerMapperRegistryInterface
{
    /** @var \Ibexa\Contracts\OAuth2Client\ResourceOwner\ResourceOwnerMapper[] */
    private $mappers;

    public function __construct(iterable $mappers)
    {
        $this->mappers = $mappers;
    }

    public function getResourceOwnerMapper(string $identifier): ResourceOwnerMapper
    {
        foreach ($this->mappers as $mapperKey => $mapper) {
            if ($mapperKey === $identifier) {
                return $mapper;
            }
        }

        throw new MapperNotFoundException($identifier);
    }
}

class_alias(ResourceOwnerMapperRegistry::class, 'Ibexa\Platform\OAuth2Client\ResourceOwner\ResourceOwnerMapperRegistry');
