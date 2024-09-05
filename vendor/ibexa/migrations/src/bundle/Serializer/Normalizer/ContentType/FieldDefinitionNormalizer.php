<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\ContentType;

use Ibexa\Core\Repository\Strategy\ContentThumbnail\Field\ContentFieldStrategy;
use Ibexa\Migration\Service\FieldTypeServiceInterface;
use Ibexa\Migration\ValueObject\ContentType\FieldDefinition;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class FieldDefinitionNormalizer implements NormalizerInterface, NormalizerAwareInterface, DenormalizerInterface, DenormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use NormalizerAwareTrait;

    use DenormalizerAwareTrait;

    /** @var \Ibexa\Migration\Service\FieldTypeServiceInterface */
    protected $fieldTypeService;

    /** @var \Ibexa\Core\Repository\Strategy\ContentThumbnail\Field\ContentFieldStrategy */
    private $contentFieldStrategy;

    public function __construct(
        FieldTypeServiceInterface $fieldTypeService,
        ContentFieldStrategy $contentFieldStrategy
    ) {
        $this->fieldTypeService = $fieldTypeService;
        $this->contentFieldStrategy = $contentFieldStrategy;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        /** @var \Ibexa\Migration\ValueObject\ContentType\FieldDefinition $object */
        $defaultValue = $this->fieldTypeService->getHashFromFieldValue($object->defaultValue, $object->type);
        $fieldData = [
            'identifier' => $object->identifier,
            'type' => $object->type,
            'position' => $object->position,
            'translations' => $object->translations,
            'required' => $object->required,
            'searchable' => $object->searchable,
            'infoCollector' => $object->infoCollector,
            'translatable' => $object->translatable,
            'category' => $object->category,
            'defaultValue' => $defaultValue,
            'fieldSettings' => $this->fieldTypeService->getFieldSettingsToHash($object->fieldSettings, $object->type),
            'validatorConfiguration' => $object->validatorConfiguration,
        ];

        return $this->setThumbnail($fieldData, $object->type, $object->thumbnail);
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $fieldData = [
            'identifier' => $data['identifier'],
            'newIdentifier' => $data['newIdentifier'] ?? null,
            'type' => $data['type'],
            'required' => $data['required'] ?? null,
            'translations' => $this->removeEmptyDescription($data['translations'] ?? []),
            'position' => $data['position'] ?? null,
            'searchable' => $data['searchable'] ?? null,
            'infoCollector' => $data['infoCollector'] ?? null,
            'translatable' => $data['translatable'] ?? null,
            'category' => $data['category'] ?? null,
            'defaultValue' => $data['defaultValue'] ?? null,
            'fieldSettings' => $data['fieldSettings'] ?? null,
            'validatorConfiguration' => $data['validatorConfiguration'] ?? [],
        ];

        if (isset($data['thumbnail'])) {
            $fieldData = $this->setThumbnail($fieldData, $data['type'], $data['thumbnail']);
        }

        return FieldDefinition::createFromArray($fieldData);
    }

    /**
     * @param array<mixed> $fieldData
     *
     * @return array<mixed>
     */
    private function setThumbnail(array $fieldData, string $type, ?bool $thumbnail): array
    {
        if ($this->contentFieldStrategy->hasStrategy($type)) {
            $fieldData['thumbnail'] = $thumbnail;
        }

        return $fieldData;
    }

    /**
     * @param array<string, array<string, string>> $translations
     *
     * @return array<string, array<string, string>>
     */
    private function removeEmptyDescription(array $translations): array
    {
        /**
         * @param array<string, string> $translation
         *
         * @return array<string, string>
         */
        $removeEmptyDescription = static function (array $translation): array {
            if (false === isset($translation['description'])) {
                unset($translation['description']);
            }

            return $translation;
        };

        return array_map($removeEmptyDescription, $translations);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof FieldDefinition;
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === FieldDefinition::class;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(FieldDefinitionNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\ContentType\FieldDefinitionNormalizer');
