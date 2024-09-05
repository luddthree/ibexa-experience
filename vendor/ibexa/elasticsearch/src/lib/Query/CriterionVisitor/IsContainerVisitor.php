<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;

final class IsContainerVisitor extends AbstractTermVisitor
{
    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof Criterion\IsContainer;
    }

    protected function getTargetField(Criterion $criterion): string
    {
        return 'content_type_is_container_b';
    }

    protected function getTargetValue(Criterion $criterion): bool
    {
        $value = $criterion->value;

        if (!is_array($value) || !is_bool($value[0])) {
            throw new \LogicException(sprintf(
                '%s value should be of type array<bool>, received %s.',
                Criterion\IsContainer::class,
                get_debug_type($value),
            ));
        }

        return $value[0];
    }
}
