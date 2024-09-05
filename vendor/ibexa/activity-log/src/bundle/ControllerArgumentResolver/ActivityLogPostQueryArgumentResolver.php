<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\ControllerArgumentResolver;

use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Query;
use Ibexa\Rest\Input\Dispatcher;
use Ibexa\Rest\Message;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class ActivityLogPostQueryArgumentResolver implements ArgumentValueResolverInterface
{
    private Dispatcher $dispatcher;

    public function __construct(
        Dispatcher $dispatcher
    ) {
        $this->dispatcher = $dispatcher;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return Query::class === $argument->getType()
            && 'query' === $argument->getName()
            && $request->isMethod('POST')
            && $request->getContent() !== null;
    }

    /**
     * @return iterable<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\Query>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        yield $this->dispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );
    }
}
