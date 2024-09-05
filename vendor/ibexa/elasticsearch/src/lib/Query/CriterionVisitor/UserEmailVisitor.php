<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\DocumentMapper\EventSubscriber\UserContentDocumentMapper;

final class UserEmailVisitor extends AbstractTermsVisitor
{
    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof Criterion\UserEmail;
    }

    protected function getTargetValue($value)
    {
        $valueAsArray = parent::getTargetValue($value);

        return array_map(static function ($value): string {
            return hash(UserContentDocumentMapper::HASHING_ALGORITHM, (string) $value);
        }, $valueAsArray);
    }

    protected function getTargetField(Criterion $criterion): string
    {
        return 'user_email_id';
    }
}
