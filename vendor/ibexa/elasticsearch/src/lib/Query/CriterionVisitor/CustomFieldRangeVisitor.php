<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\CustomField;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;

/**
 * Visits the \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\CustomField criterion
 * with LT, LTE, GT, GTE or BETWEEN operator.
 */
final class CustomFieldRangeVisitor extends AbstractRangeVisitor
{
    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof CustomField && $this->isRangeOperator($criterion->operator);
    }

    protected function getTargetField(Criterion $criterion): string
    {
        return $criterion->target;
    }
}

class_alias(CustomFieldRangeVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\CustomFieldRangeVisitor');
