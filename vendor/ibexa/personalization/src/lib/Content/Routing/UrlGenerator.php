<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Content\Routing;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Personalization\Config\Host\HostResolverInterface;

/**
 * @internal
 */
final class UrlGenerator implements UrlGeneratorInterface
{
    private const CONTENT_URL_PREFIX = '%s/api/ibexa/v2/personalization/v1/content/%s/%s%s';

    private const CONTENT_ID_URL_PREFIX = 'id';
    private const CONTENT_REMOTE_ID_URL_PREFIX = 'remote-id';
    private const CONTENT_LIST_URL_PREFIX = 'list';

    private ContentService $contentService;

    private HostResolverInterface $hostResolver;

    public function __construct(
        ContentService $contentService,
        HostResolverInterface $hostResolver
    ) {
        $this->contentService = $contentService;
        $this->hostResolver = $hostResolver;
    }

    public function generate(
        Content $content,
        bool $useRemoteId,
        ?string $lang = null
    ): string {
        return $this->getContentUrl(
            $content,
            $this->getPrefix($useRemoteId),
            $this->getContentId($content->contentInfo, $useRemoteId),
            $lang ?? $content->getDefaultLanguageCode()
        );
    }

    public function generateForContentIds(
        array $contentIds,
        string $lang
    ): string {
        $contentId = $contentIds[0];
        $content = $this->contentService->loadContent($contentId, [$lang]);

        return $this->getContentUrl(
            $content,
            self::CONTENT_LIST_URL_PREFIX,
            implode(',', $contentIds),
            $lang
        );
    }

    private function getContentUrl(
        Content $content,
        string $prefix,
        string $contentIds,
        string $lang
    ): string {
        return sprintf(
            self::CONTENT_URL_PREFIX,
            $this->hostResolver->resolveUrl($content, $lang),
            $prefix,
            $contentIds,
            '?lang=' . $lang
        );
    }

    private function getContentId(ContentInfo $contentInfo, bool $useRemoteId): string
    {
        return $useRemoteId ? $contentInfo->remoteId : (string)$contentInfo->getId();
    }

    private function getPrefix(bool $useRemoteId): string
    {
        return $useRemoteId ? self::CONTENT_REMOTE_ID_URL_PREFIX : self::CONTENT_ID_URL_PREFIX;
    }
}
