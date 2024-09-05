<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor\Location;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Location\IsMainLocation;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\Query\CriterionVisitor\AbstractTermVisitor;

final class IsMainLocationVisitor extends AbstractTermVisitor
{
    protected function getTargetField(Criterion $criterion): string
    {
        return 'is_main_location_b';
    }

    protected function getTargetValue(Criterion $criterion): bool
    {
        return $criterion->value[0] === IsMainLocation::MAIN;
    }

    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof IsMainLocation;
    }
}

class_alias(IsMainLocationVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\Location\IsMainLocationVisitor');
