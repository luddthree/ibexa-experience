<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Personalization\Content;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Personalization\Config\Authentication\ParametersResolverInterface;
use Ibexa\Personalization\Config\ItemType\IncludedItemTypeResolverInterface;
use Ibexa\Personalization\Request\Item\ItemIdsPackage;
use Ibexa\Personalization\Request\Item\PackageList;
use Ibexa\Personalization\Request\Item\UriPackage;
use Ibexa\Personalization\Service\Item\ItemServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Webmozart\Assert\Assert;

/**
 * @internal
 *
 * Provides common logic for processing content items. For use by Personalization and Product Catalog.
 */
abstract class AbstractContentService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private SettingServiceInterface $settingService;

    private IncludedItemTypeResolverInterface $includedItemTypeResolver;

    private ItemServiceInterface $itemService;

    private ParametersResolverInterface $parametersResolver;

    public function __construct(
        SettingServiceInterface $settingService,
        IncludedItemTypeResolverInterface $includedItemTypeResolver,
        ItemServiceInterface $itemService,
        ParametersResolverInterface $parametersResolver,
        ?LoggerInterface $logger = null
    ) {
        $this->settingService = $settingService;
        $this->includedItemTypeResolver = $includedItemTypeResolver;
        $this->itemService = $itemService;
        $this->parametersResolver = $parametersResolver;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @return array<string>
     */
    public function getLanguageCodesFromVersionInfo(VersionInfo $versionInfo): array
    {
        $languageCodes = [];
        foreach ($versionInfo->getLanguages() as $language) {
            $languageCodes[] = $language->getLanguageCode();
        }

        return $languageCodes;
    }

    /**
     * @param array<array{
     *     authenticationParameters: \Ibexa\Personalization\Value\Authentication\Parameters,
     *     packageList: array<\Ibexa\Personalization\Request\Item\AbstractPackage>
     * }> $updateData
     *
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    protected function update(array $updateData): void
    {
        foreach ($updateData as $data) {
            $this->itemService->update(
                $data['authenticationParameters'],
                new PackageList($data['packageList'])
            );
        }
    }

    /**
     * @param array<array{
     *     authenticationParameters: \Ibexa\Personalization\Value\Authentication\Parameters,
     *     packageList: array<\Ibexa\Personalization\Request\Item\AbstractPackage>
     * }> $deleteData
     *
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    protected function delete(array $deleteData): void
    {
        foreach ($deleteData as $data) {
            $this->itemService->delete(
                $data['authenticationParameters'],
                new PackageList($data['packageList'])
            );
        }
    }

    protected function canProcessContentItem(Content $content): bool
    {
        if (
            !$this->settingService->isInstallationKeyFound()
            || !$this->includedItemTypeResolver->isContentIncluded($content)
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $contentItems
     */
    protected function canProcessContentItems(array $contentItems): bool
    {
        if (!$this->settingService->isInstallationKeyFound()) {
            return false;
        }

        if (empty($contentItems)) {
            $this->logArrayWithContentItemsIsEmpty();

            return false;
        }

        Assert::allIsInstanceOf($contentItems, Content::class);

        if (!$this->includedItemTypeResolver->isContentIncluded($contentItems[0])) {
            return false;
        }

        return true;
    }

    /**
     * @return array<string, \Ibexa\Personalization\Value\Authentication\Parameters>
     */
    protected function getAuthenticationParameters(Content $content): array
    {
        $authenticationParameters = $this->parametersResolver->resolveAllForContent($content);
        if (empty($authenticationParameters)) {
            $this->logAuthenticationParametersNotFound($content);
        }

        return $authenticationParameters;
    }

    protected function createUriPackage(
        ContentType $contentType,
        string $languageCode,
        string $uri
    ): UriPackage {
        $contentTypeName = $contentType->getName($languageCode) ?? $contentType->getName();

        return new UriPackage(
            $contentType->id,
            (string)$contentTypeName,
            $languageCode,
            $uri
        );
    }

    /**
     * @param array<string> $itemIds
     */
    protected function createItemIdsPackage(
        ContentType $contentType,
        array $itemIds,
        string $languageCode
    ): ItemIdsPackage {
        $contentTypeName = $contentType->getName($languageCode) ?? $contentType->getName();

        return new ItemIdsPackage(
            $contentType->id,
            (string)$contentTypeName,
            $languageCode,
            $itemIds
        );
    }

    /**
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $contentItems
     *
     * @return array<
     *      string,
     *      array<\Ibexa\Contracts\Core\Repository\Values\Content\Content>
     * >
     */
    protected function groupContentByType(array $contentItems): array
    {
        $groupedContent = [];
        foreach ($contentItems as $content) {
            if (!$this->includedItemTypeResolver->isContentIncluded($content)) {
                $this->logger->warning(
                    sprintf(
                        'Content with id: "%s" and content type identifier: "%s" is not configured as included item type and will be skipped.',
                        $content->id,
                        $content->getContentType()->identifier
                    )
                );

                continue;
            }

            $groupedContent[$content->getContentType()->identifier][] = $content;
        }

        return $groupedContent;
    }

    protected function logAuthenticationParametersNotFound(Content $content): void
    {
        $this->logger->error(
            sprintf(
                'Authentication parameters for content with id: "%d" and content type identifier: "%s" could not be found.',
                $content->id,
                $content->getContentType()->identifier
            )
        );
    }

    protected function logArrayWithContentItemsIsEmpty(): void
    {
        $this->logger->error('Provided array with content items is empty');
    }
}
