<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\FormMapper;

use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeGroupUpdateData;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AttributeGroupMapper
{
    private AttributeGroupServiceInterface $attributeGroupService;

    public function __construct(
        AttributeGroupServiceInterface $attributeGroupService
    ) {
        $this->attributeGroupService = $attributeGroupService;
    }

    /**
     * @param array<mixed> $params
     */
    public function mapToFormData(AttributeGroup $attributeGroup, array $params = []): AttributeGroupUpdateData
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $params = $optionsResolver->resolve($params);

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language|null $language */
        $language = $params['language'] ?? null;

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language|null $baseLanguage */
        $baseLanguage = $params['baseLanguage'] ?? null;

        $name = $attributeGroup->getName();
        if ($baseLanguage && $language) {
            $attributeGroupInBaseLanguage = $this->attributeGroupService->getAttributeGroup(
                $attributeGroup->getIdentifier(),
                [$baseLanguage]
            );

            $name = $attributeGroupInBaseLanguage->getName();
        }

        return new AttributeGroupUpdateData(
            $attributeGroup->getIdentifier(),
            $attributeGroup->getIdentifier(),
            $name,
            $attributeGroup->getPosition(),
            $language
        );
    }

    /**
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    private function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver
            ->setDefined(['language'])
            ->setDefined(['baseLanguage'])
            ->setAllowedTypes('baseLanguage', ['null', Language::class])
            ->setAllowedTypes('language', ['null', Language::class]);
    }
}
