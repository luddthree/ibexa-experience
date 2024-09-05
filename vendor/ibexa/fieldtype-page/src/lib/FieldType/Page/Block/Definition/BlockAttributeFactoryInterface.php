<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Definition;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;

/**
 * @phpstan-import-type TAttributeNormalized from \Ibexa\Bundle\FieldTypePage\DependencyInjection\Configuration
 * @phpstan-import-type TBlockConfiguration from \Ibexa\Bundle\FieldTypePage\DependencyInjection\Configuration
 * @phpstan-import-type TReactBlockConfiguration from \Ibexa\Bundle\FieldTypePage\DependencyInjection\Configuration
 */
interface BlockAttributeFactoryInterface
{
    /**
     * @phpstan-param non-empty-string $identifier
     * @phpstan-param non-empty-string $attributeIdentifier
     * @phpstan-param TAttributeNormalized $attribute
     * @phpstan-param TBlockConfiguration|TReactBlockConfiguration $configuration
     */
    public function create(
        string $identifier,
        string $attributeIdentifier,
        array $attribute,
        array $configuration
    ): BlockAttributeDefinition;
}
