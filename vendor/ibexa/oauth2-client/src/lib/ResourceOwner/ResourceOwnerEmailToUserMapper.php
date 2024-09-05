<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\OAuth2Client\ResourceOwner;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

final class ResourceOwnerEmailToUserMapper extends ResourceOwnerToExistingUserMapper
{
    /** @var string */
    private $emailPropertyPath;

    /** @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface */
    private $propertyAccessor;

    public function __construct(
        string $emailPropertyPath,
        ?PropertyAccessorInterface $propertyAccessor = null
    ) {
        $this->emailPropertyPath = $emailPropertyPath;
        $this->propertyAccessor = $propertyAccessor ?? PropertyAccess::createPropertyAccessor();
    }

    protected function getUsername(ResourceOwnerInterface $resourceOwner): string
    {
        return $this->propertyAccessor->getValue($resourceOwner, $this->emailPropertyPath);
    }
}

class_alias(ResourceOwnerEmailToUserMapper::class, 'Ibexa\Platform\OAuth2Client\ResourceOwner\ResourceOwnerEmailToUserMapper');
