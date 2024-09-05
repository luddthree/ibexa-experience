<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Strategy;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;

interface BlockDefinitionFactoryStrategyInterface
{
    /** @phpstan-param non-empty-string $blockType */
    public function supports(string $blockType): bool;

    /**
     * @phpstan-param non-empty-string $identifier
     */
    public function create(string $identifier, array $configuration): BlockDefinition;
}
