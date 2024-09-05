<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Model;

use Ibexa\Personalization\Value\Model\Attribute;
use Psr\Http\Message\ResponseInterface;

interface AttributeDataFetcherInterface
{
    public function fetchAttribute(
        int $customerId,
        string $licenseKey,
        string $attributeKey,
        string $attributeType = Attribute::TYPE_NOMINAL,
        ?string $attributeSource = null,
        ?string $source = null
    ): ResponseInterface;
}

class_alias(AttributeDataFetcherInterface::class, 'Ibexa\Platform\Personalization\Client\Consumer\Model\AttributeDataFetcherInterface');
