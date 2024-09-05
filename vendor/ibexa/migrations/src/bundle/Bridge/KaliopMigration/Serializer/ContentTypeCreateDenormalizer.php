<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Webmozart\Assert\Assert;

final class ContentTypeCreateDenormalizer extends AbstractDenormalizer implements DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const FIELD_REQUIRE_PROPERTY_ERROR_MESSAGE = 'Step content_type create must define %s for all included fields';

    protected function getHandledKaliopType(): string
    {
        return 'content_type';
    }

    protected function getHandledKaliopMode(): string
    {
        return 'create';
    }

    /**
     * @param array<mixed> $data
     * @param array<mixed> $context
     *
     * @return array<mixed>
     */
    protected function convertFromKaliopFormat(array $data, string $type, string $format = null, array $context = []): array
    {
        $language = $data['lang'] ?? $context['default_language'] ?? null;

        $fields = array_map(function (array $attributesItem) use ($language): array {
            return $this->prepareField($attributesItem, $language);
        }, $data['attributes']);

        $convertedData = [
            'type' => $data['type'],
            'mode' => $data['mode'],
            'metadata' => $this->prepareMetadata($data, $language),
            'fields' => $fields,
        ];

        $references = $this->denormalizer->denormalize($data['references'] ?? [], ReferenceDefinition::class . '[]', $format, $context);
        if ($references) {
            $convertedData['references'] = $references;
        }

        return $convertedData;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function prepareMetadata(array $data, string $language): array
    {
        return [
            'identifier' => $data['identifier'],
            'contentTypeGroups' => [$data['content_type_group']],
            'mainTranslation' => $language,
            'nameSchema' => $data['name_pattern'] ?? null,
            'urlAliasSchema' => $data['url_name_pattern'] ?? null,
            'container' => $data['is_container'] ?? null,
            'remoteId' => $data['remote_id'] ?? null,
            'translations' => $this->prepareTranslations($language, $data['name'], $data['description']),
        ];
    }

    /**
     * @param array<mixed> $data
     *
     * @return array<string, mixed> $context
     */
    private function prepareField(array $data, string $language): array
    {
        Assert::keyExists($data, 'identifier', self::FIELD_REQUIRE_PROPERTY_ERROR_MESSAGE);
        Assert::keyExists($data, 'type', self::FIELD_REQUIRE_PROPERTY_ERROR_MESSAGE);
        Assert::keyExists($data, 'required', self::FIELD_REQUIRE_PROPERTY_ERROR_MESSAGE);

        return [
            'identifier' => $data['identifier'],
            'type' => $data['type'],
            'position' => $data['position'] ?? null,
            'translations' => $this->prepareTranslations($language, $data['name'], $data['description'] ?? null),
            'required' => $data['required'],
            'searchable' => $data['searchable'] ?? null,
            'infoCollector' => $data['info-collector'] ?? null,
            'translatable' => isset($data['disable-translation']) ? !$data['disable-translation'] : null,
            'category' => $data['category'] ?? null,
            'defaultValue' => $data['default-value'] ?? null,
            'fieldSettings' => $data['field-settings'] ?? null,
            'validatorConfiguration' => $data['validator-configuration'] ?? null,
        ];
    }

    /**
     * @param array<string, string>|string $names
     * @param array<string, string>|string|null $descriptions
     *
     * @return array<string, array{
     *      name: string,
     *      description: string
     * }>
     */
    private function prepareTranslations(string $language, $names, $descriptions): array
    {
        if (!is_array($names)) {
            $names = [
                $language => $names,
            ];
        }

        if (!is_array($descriptions)) {
            $descriptions = [
                $language => $descriptions,
            ];
        }

        $translations = [];

        foreach ($names as $lang => $name) {
            $translations[$lang] = [
                'name' => $name,
                'description' => $descriptions[$lang] ?? '',
            ];
        }

        return $translations;
    }
}

class_alias(ContentTypeCreateDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentTypeCreateDenormalizer');
