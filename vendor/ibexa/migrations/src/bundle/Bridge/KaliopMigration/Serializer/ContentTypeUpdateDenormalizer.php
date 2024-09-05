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

final class ContentTypeUpdateDenormalizer extends AbstractDenormalizer implements DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    protected function getHandledKaliopType(): string
    {
        return 'content_type';
    }

    protected function getHandledKaliopMode(): string
    {
        return 'update';
    }

    /**
     * @param array<mixed> $data
     * @param array<mixed> $context
     *
     * @return array<mixed>
     */
    protected function convertFromKaliopFormat(array $data, string $type, string $format = null, array $context = []): array
    {
        $fields = array_map(function (array $attributesItem) use ($data): array {
            return $this->prepareField($attributesItem, $data['lang'] ?? null);
        }, $data['attributes'] ?? []);

        $convertedData = [
            'type' => $data['type'],
            'mode' => $data['mode'],
            'match' => $this->prepareMatch($data),
            'metadata' => $this->prepareMetadata($data),
            'fields' => $fields,
            'actions' => $this->prepareActions($data),
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
     * @return array{
     *      field: string,
     *      value: mixed,
     * }
     */
    private function prepareMatch(array $data): array
    {
        if (array_key_exists('identifier', $data)) {
            $value = $data['identifier'];
        } else {
            $value = $data['match']['identifier'];
        }

        return [
          'field' => 'content_type_identifier',
          'value' => $value,
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function prepareMetadata(array $data): array
    {
        $translations = [];
        if (array_key_exists('name', $data)) {
            Assert::keyExists($data, 'lang', 'Content type needs to have `lang` defined');

            $lang = $data['lang'];
            $translations[$lang] = [
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
            ];
        }

        return [
            'contentTypeGroups' => isset($data['content_type_group']) ? [$data['content_type_group']] : null,
            'mainTranslation' => $data['lang'] ?? null,
            'remoteId' => $data['remote_id'] ?? null,
            'urlAliasSchema' => $data['url_name_pattern'] ?? null,
            'nameSchema' => $data['name_pattern'] ?? null,
            'container' => $data['is_container'] ?? null,
            'translations' => $translations,
        ];
    }

    /**
     * @param array<mixed> $data
     *
     * @return array<string, mixed>
     */
    private function prepareField(array $data, ?string $lang): array
    {
        Assert::keyExists($data, 'type', 'Content type fields needs to have `type` defined');

        return [
            'identifier' => $data['identifier'],
            'type' => $data['type'],
            'position' => $data['position'] ?? null,
            'translations' => isset($data['name'])
                ? $this->prepareTranslations($data['name'], $lang, $data['description'] ?? null)
                : [],
            'required' => $data['required'] ?? null,
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
     * @param array<string, string>|string $name
     * @param array<string, string>|string|null $description
     *
     * @return array<string, array{
     *      name: string,
     *      description: string
     * }>
     */
    private function prepareTranslations($name, ?string $lang, $description = ''): array
    {
        if (is_array($name)) {
            $translations = [];
            foreach ($name as $langCode => $value) {
                $translations[$langCode] = [
                    'name' => $value,
                    'description' => is_array($description) ? $description[$langCode] ?? '' : $description ?? '',
                ];
            }

            return $translations;
        }

        $message = sprintf('In order to convert `%s` `%s`, it needs to have `lang` defined.', 'content_type', 'update');
        Assert::notNull($lang, $message);

        return [
            $lang => [
                'name' => $name,
                'description' => empty($description) ? '' : $description,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<array{
     *  action: string,
     *  value: null
     * }>
     */
    private function prepareActions(array $data): array
    {
        $actions = [];
        if (array_key_exists('remove_drafts', $data)) {
            $actions[] = [
                'action' => 'remove_drafts',
                'value' => null,
            ];
        }

        if (array_key_exists('remove_attributes', $data)) {
            foreach ($data['remove_attributes'] as $attributeIdentifier) {
                $actions[] = [
                    'action' => 'remove_field_by_identifier',
                    'value' => $attributeIdentifier,
                ];
            }
        }

        return $actions;
    }
}

class_alias(ContentTypeUpdateDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentTypeUpdateDenormalizer');
