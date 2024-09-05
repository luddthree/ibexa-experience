<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\FieldType\Page\Block\Definition;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Cache\BlockDefinitionFactoryCacheHandler;

/**
 * @internal
 */
final class CachedBlockDefinitionFactory implements BlockDefinitionFactoryInterface
{
    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Cache\BlockDefinitionFactoryCacheHandler */
    private $cacheHandler;

    public function __construct(
        BlockDefinitionFactoryCacheHandler $cacheHandler
    ) {
        $this->cacheHandler = $cacheHandler;
    }

    public function getBlockIdentifiers(): array
    {
        return $this->cacheHandler->getBlockIdentifiers();
    }

    public function hasBlockDefinition(string $identifier): bool
    {
        $blockIdentifiers = $this->cacheHandler->getBlockIdentifiers();

        return in_array($identifier, $blockIdentifiers, true);
    }

    public function getBlockDefinition(string $identifier): BlockDefinition
    {
        return $this->cacheHandler->getBlockDefinition($identifier);
    }
}
