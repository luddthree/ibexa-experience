<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Personalization\Product;

use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyEntryAssignmentServiceInterface;

final class CategoryDataProvider implements DataProviderInterface
{
    private const DATA_KEY = 'product_category';

    private TaxonomyEntryAssignmentServiceInterface $taxonomyEntryAssignmentService;

    public function __construct(
        TaxonomyEntryAssignmentServiceInterface $taxonomyEntryAssignmentService
    ) {
        $this->taxonomyEntryAssignmentService = $taxonomyEntryAssignmentService;
    }

    public function getData(ContentAwareProductInterface $product, string $languageCode): ?array
    {
        $names = [];
        $taxonomyAssignments = $this->taxonomyEntryAssignmentService->loadAssignments($product->getContent());

        if (empty($taxonomyAssignments->assignments)) {
            return null;
        }

        foreach ($taxonomyAssignments->assignments as $assignment) {
            $names[] = $assignment->entry->name;
        }

        return [
            self::DATA_KEY => $names,
        ];
    }
}
