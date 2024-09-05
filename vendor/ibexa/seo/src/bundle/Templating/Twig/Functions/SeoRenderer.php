<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Seo\Templating\Twig\Functions;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Seo\Renderer\TagRendererRegistryInterface;
use Ibexa\Contracts\Seo\Resolver\FieldReferenceResolverInterface;
use Ibexa\Contracts\Seo\Resolver\SeoTypesResolverInterface;
use Ibexa\Contracts\Seo\Value\ValueInterface;
use Ibexa\Seo\Content\SeoFieldResolverInterface;
use Ibexa\Seo\FieldType\SeoValue;
use Twig\Environment as TwigEnvironment;
use Twig\Extension\RuntimeExtensionInterface;

final class SeoRenderer implements RuntimeExtensionInterface
{
    private const NO_CONTENT = '';

    private SeoTypesResolverInterface $seoTypesResolver;

    private FieldReferenceResolverInterface $fieldReferenceResolver;

    private TwigEnvironment $twig;

    private TagRendererRegistryInterface $tagRendererRepository;

    private SeoFieldResolverInterface $seoFieldResolver;

    public function __construct(
        SeoTypesResolverInterface $seoTypesResolver,
        FieldReferenceResolverInterface $fieldReferenceResolver,
        TagRendererRegistryInterface $tagRendererRepository,
        SeoFieldResolverInterface $seoFieldResolver,
        TwigEnvironment $twig
    ) {
        $this->seoTypesResolver = $seoTypesResolver;
        $this->twig = $twig;
        $this->fieldReferenceResolver = $fieldReferenceResolver;
        $this->tagRendererRepository = $tagRendererRepository;
        $this->seoFieldResolver = $seoFieldResolver;
    }

    public function isEmpty(Content $content): bool
    {
        return $this->seoFieldResolver->getSeoField($content) === null;
    }

    public function renderSeoFields(Content $content): string
    {
        $seoField = $this->seoFieldResolver->getSeoField($content);

        if ($seoField === null) {
            return self::NO_CONTENT;
        }

        /** @var \Ibexa\Seo\FieldType\SeoValue $seoValue */
        $seoValue = $seoField->value;
        $innerValue = $seoValue->getSeoTypesValue();

        if ($innerValue === null) {
            return self::NO_CONTENT;
        }

        $renderedTags = '';
        $seoTypeValues = $this->getSeoInfoFromFieldTypeDefinition($content, $seoField->fieldDefIdentifier);
        $enabledTypes = $this->seoTypesResolver->getFieldsByTypes();

        foreach ($enabledTypes as $typeName => $typeConfig) {
            foreach ($typeConfig['fields'] as $fieldName => $fieldConfig) {
                $fieldVal = $this->getFieldValue($seoTypeValues, $innerValue, $typeName, $fieldName);

                $renderedTags = sprintf(
                    '%s%s',
                    $renderedTags,
                    $this->renderSeoTag($content, $typeName, $fieldName, $fieldVal, $fieldConfig)
                );
            }
        }

        return $renderedTags;
    }

    /**
     * @param array{
     *   label: string,
     *   type: string,
     *   key: string|null,
     * } $fieldConfig
     */
    private function renderSeoTag(
        Content $content,
        string $type,
        string $field,
        string $value,
        array $fieldConfig
    ): string {
        $resolvedValue = $this->fieldReferenceResolver->resolve($content, $value);

        return $this->tagRendererRepository->render($content, $type, $field, $resolvedValue, $fieldConfig);
    }

    public function previewSeoFields(Content $content, string $fieldDefIdentifier, SeoValue $seoValue): string
    {
        $innerValue = $seoValue->getSeoTypesValue();

        if (!$innerValue) {
            return self::NO_CONTENT;
        }

        $seoTypeValues = $this->getSeoInfoFromFieldTypeDefinition($content, $fieldDefIdentifier);
        $enabledTypes = $this->seoTypesResolver->getFieldsByTypes();
        $seoTypes = [];

        foreach ($enabledTypes as $typeName => $typeConfig) {
            $seoFields = [];

            foreach ($typeConfig['fields'] as $fieldName => $fieldConfig) {
                $fieldValue = $this->getFieldValue($seoTypeValues, $innerValue, $typeName, $fieldName);
                $resolvedFieldValue = $this->fieldReferenceResolver->resolve($content, $fieldValue);

                $seoFields[] = [
                    'label' => $fieldConfig['label'],
                    'key' => $fieldConfig['key'],
                    'name' => $fieldName,
                    'value' => [
                        'base' => $fieldValue,
                        'resolved' => $resolvedFieldValue,
                    ],
                ];
            }

            $seoTypes[] = [
                'label' => $typeConfig['label'],
                'template' => $typeConfig['template'],
                'type' => $typeName,
                'fields' => $seoFields,
            ];
        }

        return $this->twig->render('@ibexadesign/ibexa/seo/preview/field_preview.html.twig', [
            'seo_types' => $seoTypes,
            'content' => $content,
        ]);
    }

    /**
     * @return \Ibexa\Seo\Value\SeoTypeValue[]
     */
    private function getSeoInfoFromFieldTypeDefinition(Content $content, string $fieldDefIdentifier): array
    {
        $fieldDefinition = $content->getContentType()->getFieldDefinition($fieldDefIdentifier);

        if (null === $fieldDefinition) {
            return [];
        }

        /** @var \Ibexa\Seo\Value\SeoTypesValue $fieldDefTypes */
        $fieldDefTypes = $fieldDefinition
            ->getFieldSettings()['types'];

        return $fieldDefTypes->getTypes();
    }

    /**
     * @param \Ibexa\Seo\Value\SeoTypeValue[] $seoTypeValues
     */
    private function getFieldValue(
        array $seoTypeValues,
        ValueInterface $innerValue,
        string $enabledTypeName,
        string $enabledFieldName
    ): string {
        $fieldValue = self::NO_CONTENT;

        if (!empty($seoTypeValues[$enabledTypeName])) {
            $fieldValue = $seoTypeValues[$enabledTypeName]->getField($enabledFieldName) ?? $fieldValue;
        }

        if (!empty($innerValue->getSeoTypesValues()[$enabledTypeName])) {
            $fieldValue = $innerValue->getSeoTypesValues()[$enabledTypeName]->getField($enabledFieldName) ?? $fieldValue;
        }

        return $fieldValue;
    }
}
