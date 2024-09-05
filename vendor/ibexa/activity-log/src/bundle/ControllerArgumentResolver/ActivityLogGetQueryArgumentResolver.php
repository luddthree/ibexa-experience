<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\ControllerArgumentResolver;

use Generator;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Query;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class ActivityLogGetQueryArgumentResolver implements ArgumentValueResolverInterface
{
    private ParsingDispatcher $dispatcher;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        ParsingDispatcher $dispatcher,
        ConfigResolverInterface $configResolver
    ) {
        $this->dispatcher = $dispatcher;
        $this->configResolver = $configResolver;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return Query::class === $argument->getType()
            && 'query' === $argument->getName()
            && $request->isMethod('GET');
    }

    /**
     * @return \Generator<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\Query>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        $criteria = $request->query->all('filter');
        $criteria = $this->dispatcher->parse(
            $criteria,
            'application/vnd.ibexa.api.internal.activity_log.criteria',
        );

        $sortClauses = $request->query->all('sort');
        $sortClauses = $this->dispatcher->parse(
            $sortClauses,
            'application/vnd.ibexa.api.internal.activity_log.sort_clauses',
        );

        if ($request->query->has('limit')) {
            $limit = $request->query->getInt('limit');
        } else {
            $limit = $this->configResolver->getParameter('activity_log.pagination.activity_logs_limit');
        }

        yield new Query(
            $criteria,
            $sortClauses,
            $request->query->getInt('offset'),
            $limit,
        );
    }
}
