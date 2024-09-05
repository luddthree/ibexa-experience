<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\SectionId;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;

final class SectionIdVisitor extends AbstractTermsVisitor
{
    protected function getTargetField(Criterion $criterion): string
    {
        return 'section_id_id';
    }

    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof SectionId;
    }
}

class_alias(SectionIdVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\SectionIdVisitor');
