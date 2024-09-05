<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor\Image;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;

final class HeightVisitor extends AbstractImageRangeVisitor
{
    private const SEARCH_FIELD_HEIGHT = 'height';

    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof Criterion\Image\Height
            && (
                $criterion->operator === Operator::BETWEEN
                || $criterion->operator === Operator::GTE
            );
    }

    protected function getSearchFieldName(): string
    {
        return self::SEARCH_FIELD_HEIGHT;
    }
}
