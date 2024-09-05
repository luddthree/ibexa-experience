<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor\Location;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\Query\CriterionVisitor\AbstractTermVisitor;

final class VisibilityVisitor extends AbstractTermVisitor
{
    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof Criterion\Visibility;
    }

    protected function getTargetField(Criterion $criterion): string
    {
        return 'invisible_b';
    }

    protected function getTargetValue(Criterion $criterion): bool
    {
        return $criterion->value[0] === Criterion\Visibility::HIDDEN;
    }
}

class_alias(VisibilityVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\Location\VisibilityVisitor');
