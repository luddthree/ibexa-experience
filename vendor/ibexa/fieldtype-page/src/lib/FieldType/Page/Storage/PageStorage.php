<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Storage;

use Ibexa\Contracts\Core\FieldType\GatewayBasedStorage;
use Ibexa\Contracts\Core\Persistence\Content;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Ibexa\Core\Persistence\Legacy\Content\Handler as ContentHandler;
use Ibexa\FieldTypePage\Exception\PageNotFoundException;
use Ibexa\FieldTypePage\FieldType\Page\Storage\Handler\PageHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * Converter for Page field type external storage.
 *
 * @property \Ibexa\FieldTypePage\FieldType\Page\Storage\Gateway $gateway
 */
class PageStorage extends GatewayBasedStorage
{
    /** @var \Ibexa\Core\Persistence\Legacy\Content\Handler */
    private $contentHandler;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\Storage\Handler\PageHandlerInterface */
    private $pageHandler;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    public function __construct(
        Gateway $gateway,
        ContentHandler $contentHandler,
        PageHandlerInterface $pageHandler,
        LoggerInterface $logger
    ) {
        parent::__construct($gateway);

        $this->contentHandler = $contentHandler;
        $this->pageHandler = $pageHandler;
        $this->logger = $logger;
    }

    /**
     * Pages are stored for every content + version + language combination.
     *
     * {@inheritdoc}
     */
    public function storeFieldData(VersionInfo $versionInfo, Field $field, array $context)
    {
        $versionNo = (int) $versionInfo->versionNo;
        $languageCode = $field->languageCode;
        $contentId = (int) $versionInfo->contentInfo->id;

        /** @var array $page */
        $page = $field->value->externalData;

        if (null === $page) {
            return null;
        }

        $this->pageHandler->insertPage($contentId, $versionNo, $languageCode, $page);

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldData(VersionInfo $versionInfo, Field $field, array $context)
    {
        $languageCode = $field->languageCode;
        $versionNo = $versionInfo->versionNo;
        $contentId = $versionInfo->contentInfo->id;

        try {
            $page = $this->pageHandler->loadPageByContentId($contentId, $versionNo, $languageCode);
        } catch (PageNotFoundException $e) {
            $this->logger->warning($e->getMessage());

            return;
        }

        $field->value->externalData = $page;
    }

    /**
     * @param \Ibexa\Contracts\Core\Persistence\Content\VersionInfo $versionInfo
     * @param array $fieldIds
     * @param array $context
     *
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds, array $context)
    {
        $content = $this->contentHandler->load($versionInfo->contentInfo->id, $versionInfo->versionNo);
        $fields = $this->findFields($content, $fieldIds);
        $languageCodes = array_unique(array_column($fields, 'languageCode'));

        try {
            $pages = $this->pageHandler->loadPagesMappedToContent(
                $versionInfo->contentInfo->id,
                $versionInfo->versionNo,
                $languageCodes
            );
        } catch (PageNotFoundException $e) {
            $this->logger->warning($e->getMessage());

            return;
        }

        foreach ($pages as $page) {
            $this->pageHandler->removePage((int) $page['id']);
        }
    }

    /**
     * Checks if field type has external data to deal with.
     *
     * @return bool
     */
    public function hasFieldData()
    {
        return true;
    }

    /**
     * @param \Ibexa\Contracts\Core\Persistence\Content\VersionInfo $versionInfo
     * @param \Ibexa\Contracts\Core\Persistence\Content\Field $field
     * @param array $context
     *
     * @return \Ibexa\Contracts\Core\Search\Field[]|null
     */
    public function getIndexData(VersionInfo $versionInfo, Field $field, array $context)
    {
        return null;
    }

    /**
     * @param \Ibexa\Contracts\Core\Persistence\Content $content
     * @param int[] $fieldIds
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\Field[]
     */
    private function findFields(Content $content, array $fieldIds): array
    {
        $fields = [];

        foreach ($content->fields as $field) {
            if (\in_array($field->id, $fieldIds)) {
                $fields[] = $field;
            }
        }

        return $fields;
    }
}

class_alias(PageStorage::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Storage\PageStorage');
