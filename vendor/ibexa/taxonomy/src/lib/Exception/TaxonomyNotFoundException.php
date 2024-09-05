<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Exception;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;

final class TaxonomyNotFoundException extends NotFoundException
{
    public static function createWithTaxonomyName(string $taxonomyName): self
    {
        return new self(
            sprintf("Taxonomy '%s' not found.", $taxonomyName)
        );
    }
}
