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

final class LocationRemoteIdVisitor extends AbstractTermsVisitor
{
    protected function getTargetField(Criterion $criterion): string
    {
        return 'location_remote_id_id';
    }

    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof Criterion\LocationRemoteId;
    }
}

class_alias(LocationRemoteIdVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\Location\LocationRemoteIdVisitor');
