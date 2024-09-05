<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Elasticsearch\Query;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

interface CriterionVisitor
{
    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool;

    public function visit(CriterionVisitor $dispatcher, Criterion $criterion, LanguageFilter $languageFilter): array;
}

class_alias(CriterionVisitor::class, 'Ibexa\Platform\Contracts\ElasticSearchEngine\Query\CriterionVisitor');
