<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeGroupId;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;

final class ContentTypeGroupIdVisitor extends AbstractTermsVisitor
{
    protected function getTargetField(Criterion $criterion): string
    {
        return 'content_type_group_id_mid';
    }

    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof ContentTypeGroupId;
    }
}

class_alias(ContentTypeGroupIdVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\ContentTypeGroupIdVisitor');
