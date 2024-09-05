<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Search;

use Ibexa\Personalization\Value\Search\ResultList;

/**
 * @internal
 */
interface SearchServiceInterface
{
    public function searchAttributes(int $customerId, string $phrase): ResultList;
}
