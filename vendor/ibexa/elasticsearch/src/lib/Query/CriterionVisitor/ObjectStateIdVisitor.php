<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ObjectStateId;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;

final class ObjectStateIdVisitor extends AbstractTermsVisitor
{
    protected function getTargetField(Criterion $criterion): string
    {
        return 'object_state_id_mid';
    }

    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof ObjectStateId;
    }
}

class_alias(ObjectStateIdVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\ObjectStateIdVisitor');
