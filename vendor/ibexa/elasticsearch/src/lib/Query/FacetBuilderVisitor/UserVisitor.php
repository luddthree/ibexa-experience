<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\FacetBuilderVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\FacetBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\FacetBuilder\UserFacetBuilder;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

/**
 * @deprecated since eZ Platform 3.2.0, to be removed in Ibexa 4.0.0.
 */
final class UserVisitor extends AbstractTermsVisitor
{
    public function supports(FacetBuilder $builder, LanguageFilter $languageFilter): bool
    {
        return $builder instanceof UserFacetBuilder;
    }

    protected function getTargetField(FacetBuilder $builder): string
    {
        switch ($builder->type) {
            case UserFacetBuilder::OWNER:
                return 'content_owner_user_id_id';
            case UserFacetBuilder::GROUP:
                return 'content_owner_user_group_id_mid';
            case UserFacetBuilder::MODIFIER:
                return 'content_version_creator_user_id_id';
            default:
                throw new InvalidArgumentException('$type', 'Unsupported UserFacetBuilder type: ' . $builder->type);
        }
    }
}

class_alias(UserVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\FacetBuilderVisitor\UserVisitor');
