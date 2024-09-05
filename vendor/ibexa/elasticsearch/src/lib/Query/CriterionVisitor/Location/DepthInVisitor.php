<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor\Location;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Location\Depth;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\Query\CriterionVisitor\AbstractTermsVisitor;

final class DepthInVisitor extends AbstractTermsVisitor
{
    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        if ($criterion instanceof Depth) {
            return $criterion->operator === Operator::EQ || $criterion->operator === Operator::IN;
        }

        return false;
    }

    protected function getTargetField(Criterion $criterion): string
    {
        return 'depth_i';
    }
}

class_alias(DepthInVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\Location\DepthInVisitor');
