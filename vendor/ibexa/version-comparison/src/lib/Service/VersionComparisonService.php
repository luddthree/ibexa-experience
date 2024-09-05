<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Service;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\VersionComparison\FieldValueDiff;
use Ibexa\Contracts\VersionComparison\Service\VersionComparisonServiceInterface;
use Ibexa\Contracts\VersionComparison\VersionDiff;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\VersionComparison\Registry\ComparisonEngineRegistryInterface;
use Ibexa\VersionComparison\Registry\FieldRegistryInterface;
use Ibexa\VersionComparison\Result\NoComparisonResult;

final class VersionComparisonService implements VersionComparisonServiceInterface
{
    /** @var \Ibexa\VersionComparison\Registry\FieldRegistryInterface */
    private $fieldRegistry;

    /** @var \Ibexa\VersionComparison\Registry\ComparisonEngineRegistryInterface */
    private $comparisonEngineRegistry;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    public function __construct(
        FieldRegistryInterface $fieldRegistry,
        ComparisonEngineRegistryInterface $comparisonEngineRegistry,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        PermissionResolver $permissionResolver
    ) {
        $this->fieldRegistry = $fieldRegistry;
        $this->comparisonEngineRegistry = $comparisonEngineRegistry;
        $this->contentTypeService = $contentTypeService;
        $this->contentService = $contentService;
        $this->permissionResolver = $permissionResolver;
    }

    public function compare(
        VersionInfo $versionA,
        VersionInfo $versionB,
        ?string $languageCode = null
    ): VersionDiff {
        if (!$this->permissionResolver->hasAccess('comparison', 'view')) {
            throw new UnauthorizedException('comparison', 'view');
        }

        $contentAId = $versionA->getContentInfo()->id;
        $contentBId = $versionB->getContentInfo()->id;
        if ($contentAId !== $contentBId) {
            throw new InvalidArgumentException(
                '$versionB',
                sprintf(
                    'Version B (id: %d) is not version of the same content as $versionA (id: %d)',
                    $versionA->id,
                    $versionB->id
                )
            );
        }
        $languageCode = $languageCode ?? $versionA->initialLanguageCode;

        if (!in_array($languageCode, $versionA->languageCodes) || !in_array($languageCode, $versionB->languageCodes)) {
            throw new InvalidArgumentException(
                '$languageCode',
                sprintf("Language '%s' must be present in both given Versions", $languageCode)
            );
        }

        $content = $this->contentService->loadContent($contentAId, [$languageCode], $versionA->versionNo);
        $contentToCompare = $this->contentService->loadContent($contentBId, [$languageCode], $versionB->versionNo);

        $contentType = $content->getContentType();

        $fieldsDiff = [];
        foreach ($content->getFields() as $field) {
            $comparableField = $this->fieldRegistry->getType($field->fieldTypeIdentifier);
            $dataA = $comparableField->getDataToCompare(
                $field->value
            );

            try {
                $matchingField = $this->getMatchingField($field, $contentToCompare);
                $dataB = $comparableField->getDataToCompare(
                    $matchingField->value
                );
            } catch (InvalidArgumentException $e) {
                $fieldsDiff[$field->fieldDefIdentifier] = new FieldValueDiff(
                    $contentType->getFieldDefinition($field->fieldDefIdentifier),
                    new NoComparisonResult()
                );
                continue;
            }

            $engine = $this->comparisonEngineRegistry->getEngine($dataA->getType());

            $diff = new NoComparisonResult();
            if ($engine->shouldRunComparison($dataA, $dataB)) {
                $diff = $engine->compareFieldsTypeValues($dataA, $dataB);
            }

            $fieldsDiff[$field->fieldDefIdentifier] = new FieldValueDiff(
                $contentType->getFieldDefinition($field->fieldDefIdentifier),
                $diff
            );
        }

        return new VersionDiff($fieldsDiff);
    }

    private function getMatchingField(
        Field $field,
        Content $contentToCompare
    ): Field {
        foreach ($contentToCompare->getFields() as $fieldToCompare) {
            if (null === $field->id) {
                if ($fieldToCompare->getFieldDefinitionIdentifier() === $field->getFieldDefinitionIdentifier()) {
                    return $fieldToCompare;
                }
            } else {
                if ($fieldToCompare->id === $field->id) {
                    return $fieldToCompare;
                }
            }
        }

        throw new InvalidArgumentException(
            '$field',
            sprintf(
                "Field with id: '%d' was not found in content with id: '%d'",
                $field->id,
                $contentToCompare->id
            )
        );
    }
}

class_alias(VersionComparisonService::class, 'EzSystems\EzPlatformVersionComparison\Service\VersionComparisonService');
