<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\QueryType;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\QueryType\OptionsResolverBasedQueryType;
use Ibexa\Core\QueryType\QueryType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsersQueryType extends OptionsResolverBasedQueryType implements QueryType
{
    private const LIMIT = 10;

    public function doGetQuery(array $parameters = []): Query
    {
        $criteria = [
            new Criterion\ContentTypeIdentifier('user'),
            new Criterion\FullText($parameters['query'] . '*'),
        ];

        if (isset($parameters['groupId'])) {
            $criteria[] = new Criterion\ParentLocationId($parameters['groupId']);
        }

        $query = new Query();
        $query->query = new Criterion\LogicalAnd($criteria);
        $query->limit = $parameters['limit'];

        return $query;
    }

    public static function getName(): string
    {
        return 'IbexaWorkflow::Users';
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined(['query', 'groupId', 'limit']);
        $resolver->setRequired('query');
        $resolver->setAllowedTypes('query', 'string');
        $resolver->setAllowedTypes('groupId', 'int');
        $resolver->setAllowedTypes('limit', 'int');
        $resolver->setDefault('limit', self::LIMIT);
    }
}

class_alias(UsersQueryType::class, 'EzSystems\EzPlatformWorkflow\QueryType\UsersQueryType');
