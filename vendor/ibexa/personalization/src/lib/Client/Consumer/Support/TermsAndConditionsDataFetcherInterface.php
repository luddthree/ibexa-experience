<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Support;

use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
interface TermsAndConditionsDataFetcherInterface
{
    public function fetchTermsAndConditions(): ResponseInterface;
}

class_alias(TermsAndConditionsDataFetcherInterface::class, 'Ibexa\Platform\Personalization\Client\Consumer\Support\TermsAndConditionsDataFetcherInterface');
