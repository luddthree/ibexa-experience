<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\SiteFactory\Values\Site;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * @property-read string|null $host
 * @property-read string|null $path
 */
final class SiteAccessMatcherConfiguration extends ValueObject
{
    /** @var string|null */
    protected $host;

    /** @var string|null */
    protected $path;

    public function __construct(?string $host = null, ?string $path = null)
    {
        parent::__construct();

        $this->host = $host;
        $this->path = $path;
    }

    public function getUrl(): string
    {
        $url = $this->host ?? '';

        if ($this->host !== null && $this->path !== null) {
            $url .= '/' . $this->path;
        }

        return $url;
    }
}

class_alias(SiteAccessMatcherConfiguration::class, 'EzSystems\EzPlatformSiteFactory\Values\Site\SiteAccessMatcherConfiguration');
