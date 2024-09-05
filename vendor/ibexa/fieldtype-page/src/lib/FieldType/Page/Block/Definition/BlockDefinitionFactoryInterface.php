<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\FieldType\Page\Block\Definition;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;

interface BlockDefinitionFactoryInterface
{
    /** @return array<string> */
    public function getBlockIdentifiers(): array;

    /** @phpstan-param non-empty-string $identifier */
    public function hasBlockDefinition(string $identifier): bool;

    /** @phpstan-param non-empty-string $identifier */
    public function getBlockDefinition(string $identifier): BlockDefinition;
}
