<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\News;

use DateTime;
use Ibexa\Dashboard\News\Values\NewsItem;
use SimpleXMLElement;

final class IbexaNewsMapper implements NewsMapperInterface
{
    public function map(SimpleXMLElement $element): NewsItem
    {
        $newsItem = new NewsItem();
        $newsItem->title = (string)$element->title;
        $newsItem->link = (string)$element->link;
        $newsItem->publicationDate = new DateTime((string)$element->pubDate);

        if ($element->enclosure->attributes() !== null) {
            $newsItem->imageUrl = (string)$element->enclosure->attributes()->url;
        }

        return $newsItem;
    }
}
