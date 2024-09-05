<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Elasticsearch\Query;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\FacetBuilder;

/**
 * @deprecated since eZ Platform 3.2.0, to be removed in Ibexa 4.0.0.
 */
interface FacetBuilderVisitor
{
    public function supports(FacetBuilder $builder, LanguageFilter $languageFilter): bool;

    public function visit(FacetBuilderVisitor $dispatcher, FacetBuilder $builder, LanguageFilter $languageFilter): array;
}

class_alias(FacetBuilderVisitor::class, 'Ibexa\Platform\Contracts\ElasticSearchEngine\Query\FacetBuilderVisitor');
