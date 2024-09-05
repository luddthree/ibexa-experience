<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor\Location;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\Query\CriterionVisitor\AbstractTermsVisitor;

final class ParentLocationIdVisitor extends AbstractTermsVisitor
{
    protected function getTargetField(Criterion $criterion): string
    {
        return 'parent_id_id';
    }

    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof Criterion\ParentLocationId;
    }
}

class_alias(ParentLocationIdVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\Location\ParentLocationIdVisitor');
