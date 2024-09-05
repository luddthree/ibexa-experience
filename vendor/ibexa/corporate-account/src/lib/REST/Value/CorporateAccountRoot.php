<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Value;

use ArrayIterator;
use Ibexa\Rest\Value as RestValue;
use IteratorAggregate;

/**
 * @internal
 */
final class CorporateAccountRoot extends RestValue implements IteratorAggregate
{
    /** @var array<\Ibexa\CorporateAccount\REST\Value\Link> */
    private array $links;

    /**
     * @param array<\Ibexa\CorporateAccount\REST\Value\Link> $links
     */
    public function __construct(array $links)
    {
        $this->links = $links;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->links);
    }
}
