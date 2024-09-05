<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\FacetBuilderVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\FacetBuilder;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;

/**
 * @deprecated since eZ Platform 3.2.0, to be removed in Ibexa 4.0.0.
 */
final class SectionVisitor extends AbstractTermsVisitor
{
    public function supports(FacetBuilder $builder, LanguageFilter $languageFilter): bool
    {
        return $builder instanceof FacetBuilder\SectionFacetBuilder;
    }

    protected function getTargetField(FacetBuilder $builder): string
    {
        return 'section_id_id';
    }
}

class_alias(SectionVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\FacetBuilderVisitor\SectionVisitor');
