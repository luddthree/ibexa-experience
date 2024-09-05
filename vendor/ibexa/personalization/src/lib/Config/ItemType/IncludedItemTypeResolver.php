<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Config\ItemType;

use Ibexa\AdminUi\Siteaccess\SiteaccessResolverInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class IncludedItemTypeResolver implements IncludedItemTypeResolverInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private const INCLUDED_ITEM_TYPES_PARAMETER = 'personalization.included_item_types';

    private ConfigResolverInterface $configResolver;

    private SiteaccessResolverInterface $siteaccessResolver;

    public function __construct(
        ConfigResolverInterface $configResolver,
        SiteaccessResolverInterface $siteaccessResolver,
        ?LoggerInterface $logger = null
    ) {
        $this->configResolver = $configResolver;
        $this->siteaccessResolver = $siteaccessResolver;
        $this->logger = $logger ?? new NullLogger();
    }

    public function resolve(array $inputItemTypes, bool $useLogger, ?string $siteAccess = null): array
    {
        $includedItemTypes = $this->getConfiguredItemTypes($siteAccess);
        $notIncludedItemTypes = array_diff($inputItemTypes, $includedItemTypes);

        if ($useLogger && !empty($notIncludedItemTypes)) {
            $this->logger->warning(sprintf(
                'Item types: %s are not configured as included item types'
                . ' and have been removed from resolving criteria',
                implode(', ', $notIncludedItemTypes)
            ));
        }

        return array_intersect($includedItemTypes, $inputItemTypes);
    }

    public function isContentIncluded(Content $content): bool
    {
        foreach ($this->siteaccessResolver->getSiteAccessesListForContent($content) as $siteAccess) {
            if ($this->isContentTypeIncludedInSiteAccess($content->getContentType(), $siteAccess->name)) {
                return true;
            }
        }

        return false;
    }

    public function isContentTypeIncludedInSiteAccess(
        ContentType $contentType,
        string $siteAccessName
    ): bool {
        return !empty(
            $this->resolve(
                [$contentType->identifier],
                false,
                $siteAccessName
            )
        );
    }

    /**
     * @return array<string>
     */
    private function getConfiguredItemTypes(?string $siteAccess = null): array
    {
        return $this->configResolver->getParameter(
            self::INCLUDED_ITEM_TYPES_PARAMETER,
            null,
            $siteAccess
        );
    }
}
