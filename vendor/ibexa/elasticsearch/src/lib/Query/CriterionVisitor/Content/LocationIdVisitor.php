<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor\Content;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\Query\CriterionVisitor\AbstractTermsVisitor;

final class LocationIdVisitor extends AbstractTermsVisitor
{
    protected function getTargetField(Criterion $criterion): string
    {
        return 'location_id_mid';
    }

    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof Criterion\LocationId;
    }
}

class_alias(LocationIdVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\Content\LocationIdVisitor');
