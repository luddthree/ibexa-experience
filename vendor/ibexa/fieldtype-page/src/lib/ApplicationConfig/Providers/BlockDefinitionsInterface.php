<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\ApplicationConfig\Providers;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;

interface BlockDefinitionsInterface
{
    public function getConfig(?ContentType $filterBy = null): array;
}
