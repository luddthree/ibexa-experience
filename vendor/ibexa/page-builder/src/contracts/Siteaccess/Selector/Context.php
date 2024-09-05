<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\PageBuilder\Siteaccess\Selector;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;

class Context
{
    public ?Language $language;

    public ?Content $content;

    public ?Location $location;

    public function __construct(
        ?Language $language = null,
        ?Content $content = null,
        ?Location $location = null
    ) {
        $this->language = $language;
        $this->content = $content;
        $this->location = $location;
    }
}
