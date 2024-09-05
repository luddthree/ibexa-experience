<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\SiteFactory\Values\Site;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class SiteUpdateStruct extends ValueObject
{
    /** @var string */
    protected $siteName;

    /** @var \Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess */
    protected $publicAccesses;

    public function __construct(
        string $siteName,
        array $publicAccesses
    ) {
        parent::__construct();

        $this->publicAccesses = $publicAccesses;
        $this->siteName = $siteName;
    }
}

class_alias(SiteUpdateStruct::class, 'EzSystems\EzPlatformSiteFactory\Values\Site\SiteUpdateStruct');
