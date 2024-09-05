<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\SystemInfo\SystemInfo\Collector;

use Ibexa\Bundle\SystemInfo\SystemInfo\Collector\ServicesSystemInfoCollector;
use Ibexa\Bundle\SystemInfo\SystemInfo\Value\ServicesSystemInfo;
use Ibexa\SystemInfo\Service\ServiceProviderInterface;
use PHPUnit\Framework\TestCase;

final class ServicesSystemInfoCollectorTest extends TestCase
{
    /**
     * @var \Ibexa\SystemInfo\Service\ServiceProviderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private ServiceProviderInterface $serviceProviderMock;

    /**
     * @var \Ibexa\Bundle\SystemInfo\SystemInfo\Collector\ServicesSystemInfoCollector
     */
    private ServicesSystemInfoCollector $serviceCollector;

    protected function setUp(): void
    {
        $this->serviceProviderMock = $this->createMock(ServiceProviderInterface::class);
        $this->serviceCollector = new ServicesSystemInfoCollector($this->serviceProviderMock);
    }

    public function testCollect(): void
    {
        $expected = new ServicesSystemInfo(
            'solr',
            'varnish',
            'redis'
        );

        $this->serviceProviderMock
            ->expects($this->exactly(3))
            ->method('getServiceType')
            ->withConsecutive(
                ['searchEngine'],
                ['httpCacheProxy'],
                ['persistenceCacheAdapter'],
            )
            ->willReturnOnConsecutiveCalls(
                $expected->getSearchEngine(),
                $expected->getHttpCacheProxy(),
                $expected->getPersistenceCacheAdapter(),
            );

        $value = $this->serviceCollector->collect();

        self::assertEquals($expected, $value);
    }
}
