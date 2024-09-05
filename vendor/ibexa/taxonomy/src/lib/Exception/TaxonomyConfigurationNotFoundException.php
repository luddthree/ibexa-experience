<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Exception;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;

final class TaxonomyConfigurationNotFoundException extends NotFoundException
{
    public static function create(string $taxonomyName, string $configName): self
    {
        return new self(
            sprintf("Configuration '%s' of taxonomy '%s' not found.", $configName, $taxonomyName)
        );
    }
}
