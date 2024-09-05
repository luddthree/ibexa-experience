<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ContentType;

/**
 * @internal
 */
interface ContentTypeFinderRegistryInterface
{
    /**
     * @param string $field
     */
    public function hasFinder(string $field): bool;

    /**
     * @param string $field
     *
     * @throws \LogicException if not found
     */
    public function getFinder(string $field): ContentTypeFinderInterface;
}

class_alias(ContentTypeFinderRegistryInterface::class, 'Ibexa\Platform\Migration\StepExecutor\ContentType\ContentTypeFinderRegistryInterface');
