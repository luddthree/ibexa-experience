<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\Search;

use Ibexa\Contracts\Connector\Dam\Search\Query;
use Ibexa\Contracts\Connector\Dam\Search\QueryFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class GenericQueryFactory implements QueryFactory
{
    public function buildFromRequest(Request $request): Query
    {
        if (null === $request->get('query')) {
            throw new BadRequestHttpException('Request do not contain all required arguments');
        }

        return new Query(
            $request->get('query')
        );
    }
}

class_alias(GenericQueryFactory::class, 'Ibexa\Platform\Connector\Dam\Search\GenericQueryFactory');
