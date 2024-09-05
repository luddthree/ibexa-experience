<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\UserMetadata;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;

final class UserMetadataVisitor extends AbstractTermsVisitor
{
    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof UserMetadata;
    }

    protected function getTargetField(Criterion $criterion): string
    {
        switch ($criterion->target) {
            case UserMetadata::MODIFIER:
                return 'content_version_creator_user_id_id';
            case UserMetadata::OWNER:
                return 'content_owner_user_id_id';
            case UserMetadata::GROUP:
                return 'content_owner_user_group_id_mid';
        }

        throw new NotImplementedException(
            'No visitor available for target: ' . $criterion->target . ' with operator: ' . $criterion->operator
        );
    }
}

class_alias(UserMetadataVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\UserMetadataVisitor');
