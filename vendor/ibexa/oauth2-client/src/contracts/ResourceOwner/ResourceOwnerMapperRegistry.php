<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\OAuth2Client\ResourceOwner;

interface ResourceOwnerMapperRegistry
{
    /**
     * Returns resource owner mapper associated with given OAuth2 client.
     *
     * @throws \Ibexa\Contracts\OAuth2Client\Exception\ResourceOwner\MapperNotFoundException
     */
    public function getResourceOwnerMapper(string $identifier): ResourceOwnerMapper;
}

class_alias(ResourceOwnerMapperRegistry::class, 'Ibexa\Platform\Contracts\OAuth2Client\ResourceOwner\ResourceOwnerMapperRegistry');
