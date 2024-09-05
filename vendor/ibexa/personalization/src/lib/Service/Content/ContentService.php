<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Content;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Personalization\Content\AbstractContentService;
use Ibexa\Personalization\Config\Authentication\ParametersResolverInterface;
use Ibexa\Personalization\Config\ItemType\IncludedItemTypeResolverInterface;
use Ibexa\Personalization\Config\Repository\RepositoryConfigResolverInterface;
use Ibexa\Personalization\Content\Routing\UrlGeneratorInterface;
use Ibexa\Personalization\Service\Item\ItemServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;

/**
 * @internal
 */
final class ContentService extends AbstractContentService implements ContentServiceInterface
{
    private RepositoryConfigResolverInterface $repositoryConfigResolver;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        ParametersResolverInterface $parametersResolver,
        RepositoryConfigResolverInterface $repositoryConfigResolver,
        IncludedItemTypeResolverInterface $includedItemTypeResolver,
        ItemServiceInterface $itemService,
        SettingServiceInterface $settingService,
        UrlGeneratorInterface $urlGenerator
    ) {
        parent::__construct(
            $settingService,
            $includedItemTypeResolver,
            $itemService,
            $parametersResolver
        );

        $this->repositoryConfigResolver = $repositoryConfigResolver;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function updateContent(Content $content, ?array $languageCodes = null): void
    {
        if (!$this->canProcessContentItem($content)) {
            return;
        }

        $authenticationParameters = $this->getAuthenticationParameters($content);
        if (empty($authenticationParameters)) {
            return;
        }

        if (empty($languageCodes)) {
            $languageCodes = $this->getLanguageCodesFromVersionInfo($content->getVersionInfo());
        }

        $updateData = [];
        foreach ($languageCodes as $languageCode) {
            if (!isset($authenticationParameters[$languageCode])) {
                continue;
            }

            $authenticationParametersForLanguage = $authenticationParameters[$languageCode];
            $customerId = $authenticationParametersForLanguage->getCustomerId();
            $updateData[$customerId]['authenticationParameters'] = $authenticationParametersForLanguage;
            $updateData[$customerId]['packageList'][] = $this->createUriPackage(
                $content->getContentType(),
                $languageCode,
                $this->urlGenerator->generate(
                    $content,
                    $this->repositoryConfigResolver->useRemoteId(),
                    $languageCode
                )
            );
        }

        $this->update($updateData);
    }

    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function updateContentItems(array $contentItems): void
    {
        if (!$this->canProcessContentItems($contentItems)) {
            return;
        }

        $updateData = [];
        foreach ($this->groupContentByType($contentItems) as $group) {
            $contentIds = [];
            $authenticationParameters = [];

            /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
            foreach ($group as $content) {
                $authenticationParameters = $this->getAuthenticationParameters($content);
                if (empty($authenticationParameters)) {
                    continue;
                }

                $contentIds[] = $content->getVersionInfo()->getContentInfo()->getId();
            }

            if (empty($contentIds)) {
                continue;
            }

            $content = $group[0];
            foreach ($this->getLanguageCodesFromVersionInfo($content->getVersionInfo()) as $languageCode) {
                $authenticationParametersForLanguage = $authenticationParameters[$languageCode];
                $customerId = $authenticationParametersForLanguage->getCustomerId();

                $updateData[$customerId]['authenticationParameters'] = $authenticationParametersForLanguage;
                $updateData[$customerId]['packageList'][] = $this->createUriPackage(
                    $content->getContentType(),
                    $languageCode,
                    $this->urlGenerator->generateForContentIds($contentIds, $languageCode)
                );
            }
        }

        $this->update($updateData);
    }

    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function deleteContent(Content $content, array $languageCodes): void
    {
        if (!$this->canProcessContentItem($content)) {
            return;
        }

        $authenticationParameters = $this->getAuthenticationParameters($content);
        if (empty($authenticationParameters)) {
            return;
        }

        $contentInfo = $content->getVersionInfo()->getContentInfo();
        $itemIds[] = $this->repositoryConfigResolver->useRemoteId()
            ? $contentInfo->remoteId
            : (string)$contentInfo->getId();

        $deleteData = [];
        foreach ($languageCodes as $languageCode) {
            if (!isset($authenticationParameters[$languageCode])) {
                continue;
            }

            $authenticationParametersForLanguage = $authenticationParameters[$languageCode];
            $customerId = $authenticationParametersForLanguage->getCustomerId();
            $deleteData[$customerId]['authenticationParameters'] = $authenticationParametersForLanguage;
            $deleteData[$customerId]['packageList'][] = $this->createItemIdsPackage(
                $content->getContentType(),
                $itemIds,
                $languageCode
            );
        }

        $this->delete($deleteData);
    }

    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function deleteContentItems(array $contentItems): void
    {
        if (!$this->canProcessContentItems($contentItems)) {
            return;
        }

        $deleteData = [];
        foreach ($this->groupContentByType($contentItems) as $group) {
            $itemIds = [];
            $authenticationParameters = [];

            /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
            foreach ($group as $content) {
                $authenticationParameters = $this->getAuthenticationParameters($content);
                if (empty($authenticationParameters)) {
                    continue;
                }

                $contentInfo = $content->getVersionInfo()->getContentInfo();
                $itemIds[] = $this->repositoryConfigResolver->useRemoteId()
                    ? $contentInfo->remoteId
                    : (string)$contentInfo->getId();
            }

            if (empty($itemIds)) {
                continue;
            }

            $content = $group[0];
            foreach ($this->getLanguageCodesFromVersionInfo($content->getVersionInfo()) as $languageCode) {
                $authenticationParametersForLanguage = $authenticationParameters[$languageCode];
                $customerId = $authenticationParametersForLanguage->getCustomerId();

                $deleteData[$customerId]['authenticationParameters'] = $authenticationParametersForLanguage;
                $deleteData[$customerId]['packageList'][] = $this->createItemIdsPackage(
                    $content->getContentType(),
                    $itemIds,
                    $languageCode
                );
            }
        }

        $this->delete($deleteData);
    }
}
