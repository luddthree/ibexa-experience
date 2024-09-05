<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\News;

use DateTime;
use Ibexa\Contracts\Test\Core\IbexaKernelTestCase as TestCoreIbexaKernelTestCase;
use Ibexa\Dashboard\News\Feed;
use Ibexa\Dashboard\News\FeedException;
use Ibexa\Dashboard\News\IbexaNewsMapper;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class FeedTest extends TestCoreIbexaKernelTestCase
{
    public function testFetch(): void
    {
        $testCore = $this->getIbexaTestCore();
        $testFeed = file_get_contents(__DIR__ . '/../_fixtures/test_feed');
        if ($testFeed === false) {
            self::markTestSkipped(
                'Could not load test_feed fixtures'
            );
        }
        $mockResponses = new MockResponse($testFeed);
        $feed = new Feed(
            $testCore->getServiceByClassName(IbexaNewsMapper::class),
            new MockHttpClient($mockResponses, 'https://ibexa.com')
        );
        $news = $feed->fetch('https://ibexa.com', 2);
        self::assertCount(2, $news);
        $news1 = $news[0];
        self::assertSame('New features for Editors and Marketers in Ibexa DXP 4.6 beta 2', $news1->title);
        self::assertSame(
            'https://www.ibexa.co/blog/new-features-for-editors-and-marketers-in-ibexa-dxp-4.6-beta-2',
            $news1->link
        );
        self::assertEquals(
            new DateTime('2023-10-05 08:08:56 +0000'),
            $news1->publicationDate
        );
        self::assertEquals(
            'https://www.ibexa.co/var/site/storage/images/b2f3b84a5934-beta-header.png',
            $news1->imageUrl
        );
    }

    public function testFetchFailed(): void
    {
        $testCore = $this->getIbexaTestCore();
        $corruptedFeed = file_get_contents(__DIR__ . '/../_fixtures/corrupted_feed');
        if ($corruptedFeed === false) {
            self::markTestSkipped(
                'Could not load corrupted_feed fixtures'
            );
        }
        $mockResponses = new MockResponse($corruptedFeed);
        $feed = new Feed(
            $testCore->getServiceByClassName(IbexaNewsMapper::class),
            new MockHttpClient($mockResponses, 'https://ibexa.com')
        );

        $this->expectException(FeedException::class);
        $this->expectExceptionMessage('Invalid channel');

        $feed->fetch(__DIR__ . '/../_fixtures/corrupted_feed', 2);
    }
}
