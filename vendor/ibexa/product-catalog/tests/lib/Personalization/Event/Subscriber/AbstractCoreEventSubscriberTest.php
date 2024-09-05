<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Personalization\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface as APIProductServiceInterface;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Personalization\Service\Content\ContentServiceInterface;
use Ibexa\ProductCatalog\Personalization\Service\Product\ProductServiceInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class AbstractCoreEventSubscriberTest extends TestCase
{
    /** @var \Ibexa\Personalization\Service\Content\ContentServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    protected ContentServiceInterface $contentServiceMock;

    /** @var \Ibexa\ProductCatalog\Personalization\Service\Product\ProductServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    protected ProductServiceInterface $productServiceMock;

    /** @var \Ibexa\Contracts\ProductCatalog\ProductServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    protected APIProductServiceInterface $apiProductServiceMock;

    /** @var \Ibexa\Contracts\ProductCatalog\PermissionResolverInterface&\PHPUnit\Framework\MockObject\MockObject */
    protected PermissionResolverInterface $permissionResolver;

    protected ContentInfo $contentInfo;

    protected Content $content;

    public function setUp(): void
    {
        $this->contentServiceMock = $this->getMockBuilder(ContentServiceInterface::class)
            ->onlyMethods(['updateContent', 'updateContentItems'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->contentInfo = new ContentInfo([
            'id' => 1,
            'contentTypeId' => 2,
        ]);
        $this->content = new Content([
            'versionInfo' => new VersionInfo([
                'contentInfo' => $this->contentInfo,
            ]),
        ]);

        $this->productServiceMock = $this->createMock(ProductServiceInterface::class);
        $this->apiProductServiceMock = $this->createMock(APIProductServiceInterface::class);
        $this->permissionResolver = $this->createMock(PermissionResolverInterface::class);
    }

    /**
     * @dataProvider subscribedEventsDataProvider
     */
    public function testHasSubscribedEvent(string $event): void
    {
        self::assertArrayHasKey($event, $this->getEventSubscriber()::getSubscribedEvents());
    }

    abstract public function getEventSubscriber(): EventSubscriberInterface;

    /**
     * @return iterable<int, array<int, string>>
     */
    abstract public function subscribedEventsDataProvider(): iterable;
}
