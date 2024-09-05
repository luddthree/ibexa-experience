<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Security\Limitation\Loader;

interface PersonalizationLimitationListLoaderInterface
{
    /**
     * @return array<int|string>
     */
    public function getList(): array;
}

class_alias(PersonalizationLimitationListLoaderInterface::class, 'Ibexa\Platform\Personalization\Security\Limitation\Loader\PersonalizationLimitationListLoaderInterface');
