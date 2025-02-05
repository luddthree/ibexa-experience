<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Bookmark;

use Ibexa\Contracts\Core\Persistence\Bookmark\Bookmark;
use Ibexa\Contracts\Core\Persistence\Bookmark\CreateStruct;
use Ibexa\Contracts\Core\Persistence\Bookmark\Handler as HandlerInterface;

/**
 * Storage Engine handler for bookmarks.
 */
class Handler implements HandlerInterface
{
    /** @var \Ibexa\Core\Persistence\Legacy\Bookmark\Gateway */
    private $gateway;

    /** @var \Ibexa\Core\Persistence\Legacy\Bookmark\Mapper */
    private $mapper;

    /**
     * Handler constructor.
     *
     * @param \Ibexa\Core\Persistence\Legacy\Bookmark\Gateway $gateway
     * @param \Ibexa\Core\Persistence\Legacy\Bookmark\Mapper $mapper
     */
    public function __construct(Gateway $gateway, Mapper $mapper)
    {
        $this->gateway = $gateway;
        $this->mapper = $mapper;
    }

    /**
     * {@inheritdoc}
     */
    public function create(CreateStruct $createStruct): Bookmark
    {
        $bookmark = $this->mapper->createBookmarkFromCreateStruct(
            $createStruct
        );
        $bookmark->id = $this->gateway->insertBookmark($bookmark);

        return $bookmark;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $bookmarkId): void
    {
        $this->gateway->deleteBookmark($bookmarkId);
    }

    /**
     * {@inheritdoc}
     */
    public function loadByUserIdAndLocationId(int $userId, array $locationIds): array
    {
        $bookmarks = $this->mapper->extractBookmarksFromRows(
            $this->gateway->loadBookmarkDataByUserIdAndLocationId($userId, $locationIds)
        );

        $list = [];
        foreach ($bookmarks as $bookmark) {
            $list[$bookmark->locationId] = $bookmark;
        }

        return $list;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserBookmarks(int $userId, int $offset = 0, int $limit = -1): array
    {
        return $this->mapper->extractBookmarksFromRows(
            $this->gateway->loadUserBookmarks($userId, $offset, $limit)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function countUserBookmarks(int $userId): int
    {
        return $this->gateway->countUserBookmarks($userId);
    }

    /**
     * {@inheritdoc}
     */
    public function locationSwapped(int $location1Id, int $location2Id): void
    {
        $this->gateway->locationSwapped($location1Id, $location2Id);
    }
}

class_alias(Handler::class, 'eZ\Publish\Core\Persistence\Legacy\Bookmark\Handler');
