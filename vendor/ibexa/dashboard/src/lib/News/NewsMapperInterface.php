<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\News;

use Ibexa\Dashboard\News\Values\NewsItem;
use SimpleXMLElement;

interface NewsMapperInterface
{
    /**
     * @throws \Exception
     */
    public function map(SimpleXMLElement $element): NewsItem;
}
