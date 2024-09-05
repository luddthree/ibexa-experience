<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;

final class RemoteIdVisitor extends AbstractTermsVisitor
{
    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof Criterion\RemoteId;
    }

    protected function getTargetField(Criterion $criterion): string
    {
        return 'content_remote_id_id';
    }
}

class_alias(RemoteIdVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\RemoteIdVisitor');
