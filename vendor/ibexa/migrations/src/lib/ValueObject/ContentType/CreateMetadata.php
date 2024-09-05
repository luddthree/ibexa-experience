<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\ContentType;

use DateTime;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;
use Webmozart\Assert\Assert;

final class CreateMetadata
{
    /** @var mixed */
    public $identifier;

    /** @var string */
    public $mainTranslation;

    /** @var mixed */
    public $creatorId;

    /** @var \DateTime|null */
    public $creationDate;

    /** @var string */
    public $remoteId;

    /** @var string */
    public $urlAliasSchema;

    /** @var string */
    public $nameSchema;

    /** @var bool */
    public $container;

    /** @var bool */
    public $defaultAlwaysAvailable;

    /** @var int */
    public $defaultSortField;

    /** @var int */
    public $defaultSortOrder;

    /**
     * @var array<string|int>
     */
    public $contentTypeGroups;

    /**
     * @var array<string, array{
     *      name: string,
     *      description?: string
     * }>
     */
    public $translations;

    private function __construct()
    {
    }

    public static function create(ContentType $contentType): self
    {
        $vo = new self();
        $vo->identifier = $contentType->identifier;
        $vo->mainTranslation = $contentType->mainLanguageCode;
        $vo->creatorId = $contentType->creatorId;
        $vo->creationDate = $contentType->creationDate;
        $vo->remoteId = $contentType->remoteId;
        $vo->urlAliasSchema = $contentType->urlAliasSchema;
        $vo->nameSchema = $contentType->nameSchema;
        $vo->container = $contentType->isContainer;
        $vo->defaultAlwaysAvailable = $contentType->defaultAlwaysAvailable;
        $vo->defaultSortField = $contentType->defaultSortField;
        $vo->defaultSortOrder = $contentType->defaultSortOrder;
        $vo->contentTypeGroups = array_map(static function (ContentTypeGroup $contentTypeGroup): string {
            return $contentTypeGroup->identifier;
        }, $contentType->getContentTypeGroups());
        $vo->translations = self::prepareTranslations($contentType);

        return $vo;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function createFromArray(array $data): self
    {
        Assert::keyExists($data, 'identifier');
        Assert::string($data['identifier']);

        $vo = new self();
        $vo->identifier = $data['identifier'];
        $vo->translations = $data['translations'];
        $vo->contentTypeGroups = $data['contentTypeGroups'];

        $vo->mainTranslation = $data['mainTranslation'];
        $vo->creatorId = $data['creatorId'] ?? null;
        $vo->creationDate = isset($data['creationDate']) ? new DateTime($data['creationDate']) : null;
        $vo->remoteId = $data['remoteId'] ?? null;
        $vo->urlAliasSchema = $data['urlAliasSchema'] ?? null;
        $vo->nameSchema = $data['nameSchema'] ?? null;
        $vo->container = $data['container'] ?? false;
        $vo->defaultAlwaysAvailable = $data['defaultAlwaysAvailable'] ?? true;
        $vo->defaultSortField = $data['defaultSortField'] ?? null;
        $vo->defaultSortOrder = $data['defaultSortOrder'] ?? null;

        return $vo;
    }

    /**
     * @return array<string, array{
     *      name: string,
     *      description?: string
     * }>
     */
    private static function prepareTranslations(ContentType $contentType): array
    {
        $translations = [];

        foreach ($contentType->getNames() as $lang => $value) {
            $translations[$lang]['name'] = $value;
        }

        foreach ($contentType->getDescriptions() as $lang => $value) {
            $translations[$lang]['description'] = $value ?? '';
        }

        return $translations;
    }
}

class_alias(CreateMetadata::class, 'Ibexa\Platform\Migration\ValueObject\ContentType\CreateMetadata');
