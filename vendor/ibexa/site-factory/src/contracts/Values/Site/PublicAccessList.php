<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\SiteFactory\Values\Site;

use ArrayIterator;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use IteratorAggregate;
use Traversable;

/**
 * @property-read int $totalCount
 * @property-read \Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess[] $publicAccesses
 *
 * @implements \IteratorAggregate<\Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess>
 */
final class PublicAccessList extends ValueObject implements IteratorAggregate
{
    /** @var int */
    protected $totalCount;

    /**
     * @var \Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess[]
     */
    protected $publicAccesses = [];

    public function __construct(int $totalCount = 0, array $publicAccesses = [])
    {
        parent::__construct();

        $this->totalCount = $totalCount;
        $this->publicAccesses = $publicAccesses;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->publicAccesses);
    }
}

class_alias(PublicAccessList::class, 'EzSystems\EzPlatformSiteFactory\Values\Site\PublicAccessList');
