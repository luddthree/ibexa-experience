<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Taxonomy\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class TaxonomyExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'ibexa_taxonomy_name',
                [TaxonomyRuntime::class, 'getTaxonomyName'],
            ),
            new TwigFilter(
                'ibexa_taxonomy_entries_for_content',
                [TaxonomyRuntime::class, 'getTaxonomyEntriesForContent'],
            ),
        ];
    }
}
