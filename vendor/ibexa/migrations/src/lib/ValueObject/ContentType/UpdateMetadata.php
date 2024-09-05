<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\ContentType;

use DateTime;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;

final class UpdateMetadata
{
    /** @var mixed */
    public $identifier;

    /** @var string|null */
    public $mainTranslation;

    /** @var mixed */
    public $modifierId;

    /** @var \DateTime|null */
    public $modificationDate;

    /** @var string|null */
    public $remoteId;

    /** @var string|null */
    public $urlAliasSchema;

    /** @var string|null */
    public $nameSchema;

    /** @var bool|null */
    public $container;

    /** @var bool|null */
    public $defaultAlwaysAvailable;

    /** @var int|null */
    public $defaultSortField;

    /** @var int|null */
    public $defaultSortOrder;

    /**
     * @var array<string, array{
     *      name: string,
     *      description?: string
     * }>
     */
    public $translations;

    /**
     * @param mixed $identifier
     * @param mixed $modifierId
     *
     * @phpstan-param array<string, array{
     *      name: string,
     *      description?: string
     * }> $translations
     */
    private function __construct(
        $identifier,
        ?string $mainTranslation,
        $modifierId,
        ?DateTime $modificationDate,
        ?string $remoteId,
        ?string $urlAliasSchema,
        ?string $nameSchema,
        ?bool $container,
        ?bool $defaultAlwaysAvailable,
        ?int $defaultSortField,
        ?int $defaultSortOrder,
        array $translations = []
    ) {
        $this->identifier = $identifier;
        $this->mainTranslation = $mainTranslation;
        $this->modifierId = $modifierId;
        $this->modificationDate = $modificationDate;
        $this->remoteId = $remoteId;
        $this->urlAliasSchema = $urlAliasSchema;
        $this->nameSchema = $nameSchema;
        $this->container = $container;
        $this->defaultAlwaysAvailable = $defaultAlwaysAvailable;
        $this->defaultSortField = $defaultSortField;
        $this->defaultSortOrder = $defaultSortOrder;
        $this->translations = $translations;
    }

    public static function create(ContentType $contentType): self
    {
        return new self(
            $contentType->identifier,
            $contentType->mainLanguageCode,
            $contentType->creatorId,
            $contentType->creationDate,
            $contentType->remoteId,
            $contentType->urlAliasSchema,
            $contentType->nameSchema,
            $contentType->isContainer,
            $contentType->defaultAlwaysAvailable,
            $contentType->defaultSortField,
            $contentType->defaultSortOrder,
            self::prepareTranslations($contentType)
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            $data['identifier'] ?? null,
            $data['mainTranslation'] ?? null,
            $data['modifierId'] ?? null,
            isset($data['modificationDate']) ? new DateTime($data['modificationDate']) : null,
            $data['remoteId'] ?? null,
            $data['urlAliasSchema'] ?? null,
            $data['nameSchema'] ?? null,
            $data['container'] ?? null,
            $data['defaultAlwaysAvailable'] ?? null,
            $data['defaultSortField'] ?? null,
            $data['defaultSortOrder'] ?? null,
            $data['translations'] ?? [],
        );
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
            $translations[$lang]['description'] = $value;
        }

        return $translations;
    }
}

class_alias(UpdateMetadata::class, 'Ibexa\Platform\Migration\ValueObject\ContentType\UpdateMetadata');
