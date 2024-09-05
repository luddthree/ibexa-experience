<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Dashboard\News;

use DateTime;
use Ibexa\Dashboard\News\IbexaNewsMapper;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

final class IbexaNewsMapperTest extends TestCase
{
    private const NEWS_ITEM = '
        <item>
            <title>New features for Editors and Marketers</title>
            <link>https://www.ibexa.co/blog/new-features</link>
            <description>The second beta release of Ibexa DXP v4.6.0 is now available.</description>
            <pubDate>Thu, 05 Oct 2023 08:08:56 +0000</pubDate>
            <guid>https://www.ibexa.co/blog/new-features-for-editors-and-marketers-in-ibexa-dxp-4.6-beta-2</guid>
            <enclosure url="https://www.ibexa.co/var/site/storage/images/b2f3b84a5934-beta-header.png" type="image/png" length=""/>
        </item>
    ';

    public function testMap(): void
    {
        $item = new SimpleXMLElement(self::NEWS_ITEM);
        $mapper = new IbexaNewsMapper();
        $newsItem = $mapper->map($item);

        self::assertSame('New features for Editors and Marketers', $newsItem->title);
        self::assertSame('https://www.ibexa.co/blog/new-features', $newsItem->link);
        self::assertSame(
            'https://www.ibexa.co/var/site/storage/images/b2f3b84a5934-beta-header.png',
            $newsItem->imageUrl
        );
        self::assertEquals(new DateTime('Thu, 05 Oct 2023 08:08:56 +0000'), $newsItem->publicationDate);
    }
}
