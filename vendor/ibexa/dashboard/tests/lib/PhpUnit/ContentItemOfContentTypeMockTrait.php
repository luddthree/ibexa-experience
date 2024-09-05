<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Dashboard\PhpUnit;

use Ibexa\Bundle\Dashboard\DependencyInjection\Parser\Dashboard;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\Core\Repository\Values\Content\Location;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
trait ContentItemOfContentTypeMockTrait
{
    protected function mockContentItemOfDashboardType(): Content
    {
        return $this->mockContentItemOfContentType(Dashboard::DASHBOARD_CONTENT_TYPE_IDENTIFIER);
    }

    protected function mockContentInfoOfDashboardType(): ContentInfo
    {
        return $this->mockContentInfoOfContentType(Dashboard::DASHBOARD_CONTENT_TYPE_IDENTIFIER);
    }

    protected function mockLocationOfPredefinedDashboardType(): Location
    {
        return $this->mockLocationOfContentItemOfContentType(
            Dashboard::DASHBOARD_CONTENT_TYPE_IDENTIFIER,
            Dashboard::DEFAULT_DASHBOARD_REMOTE_ID
        );
    }

    protected function mockLocationOfContentItemOfDashboardType(): Location
    {
        return $this->mockLocationOfContentItemOfContentType(
            Dashboard::DASHBOARD_CONTENT_TYPE_IDENTIFIER,
            null,
            '/1/56/57/'
        );
    }

    protected function mockLocationOfContentItemOfContentType(
        string $contentTypeIdentifier,
        string $locationRemoteId = null,
        string $pathString = null
    ): Location {
        if (!$this instanceof TestCase) {
            throw new LogicException(
                sprintf('%s can be used only inside of classes extending %s', __TRAIT__, TestCase::class)
            );
        }

        $locationMock = $this->createMock(Location::class);
        $locationMock->method('getContent')->willReturn($this->mockContentItemOfContentType($contentTypeIdentifier));
        $locationMock->method('getContentInfo')->willReturn($this->mockContentInfoOfContentType($contentTypeIdentifier));
        if ($locationRemoteId !== null) {
            $locationMock
                ->method('__get')
                ->with('remoteId')
                ->willReturn($locationRemoteId);
        }
        if ($pathString !== null) {
            $locationMock
                ->method('getPathString')
                ->willReturn($pathString);
        }

        return $locationMock;
    }

    protected function mockContentItemOfContentType(string $contentTypeIdentifier): Content
    {
        if (!$this instanceof TestCase) {
            throw new LogicException(
                sprintf('%s can be used only inside of classes extending %s', __TRAIT__, TestCase::class)
            );
        }

        $contentItemMock = $this->createMock(Content::class);
        $contentItemMock->method('getContentType')->willReturn($this->mockContentType($contentTypeIdentifier));

        return $contentItemMock;
    }

    protected function mockContentInfoOfContentType(string $contentTypeIdentifier): ContentInfo
    {
        if (!$this instanceof TestCase) {
            throw new LogicException(
                sprintf('%s can be used only inside of classes extending %s', __TRAIT__, TestCase::class)
            );
        }

        $contentItemMock = $this->createMock(ContentInfo::class);
        $contentItemMock->method('getContentType')->willReturn($this->mockContentType($contentTypeIdentifier));

        return $contentItemMock;
    }

    /**
     * @param array<\Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup> $contentTypeGroups
     */
    protected function mockDashboardContentType(array $contentTypeGroups = []): ContentType
    {
        $contentType = $this->mockContentType(Dashboard::DASHBOARD_CONTENT_TYPE_IDENTIFIER);
        $contentType
            ->method('getContentTypeGroups')
            ->willReturn($contentTypeGroups);

        return $contentType;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType&\PHPUnit\Framework\MockObject\MockObject
     */
    protected function mockContentType(string $contentTypeIdentifier): ContentType
    {
        if (!$this instanceof TestCase) {
            throw new LogicException(
                sprintf('%s can be used only inside of classes extending %s', __TRAIT__, TestCase::class)
            );
        }

        $contentTypeMock = $this->createMock(ContentType::class);
        $contentTypeMock
            ->method('__get')
            ->with('identifier')
            ->willReturn($contentTypeIdentifier);

        return $contentTypeMock;
    }

    protected function mockContentTypeGroup(string $identifier): ContentTypeGroup
    {
        if (!$this instanceof TestCase) {
            throw new LogicException(
                sprintf('%s can be used only inside of classes extending %s', __TRAIT__, TestCase::class)
            );
        }

        $contentTypeGroupMock = $this->createMock(ContentTypeGroup::class);
        $contentTypeGroupMock
            ->method('__get')
            ->with('identifier')
            ->willReturn($identifier);

        return $contentTypeGroupMock;
    }
}
