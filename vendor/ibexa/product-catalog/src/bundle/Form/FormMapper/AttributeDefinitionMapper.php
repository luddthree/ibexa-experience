<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\FormMapper;

use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinitionUpdateData;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AttributeDefinitionMapper
{
    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    public function __construct(
        AttributeDefinitionServiceInterface $attributeDefinitionService
    ) {
        $this->attributeDefinitionService = $attributeDefinitionService;
    }

    /**
     * @param array<mixed> $params
     */
    public function mapToFormData(AttributeDefinition $attributeDefinition, array $params = []): AttributeDefinitionUpdateData
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $params = $optionsResolver->resolve($params);

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language|null $language */
        $language = $params['language'] ?? null;

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language|null $baseLanguage */
        $baseLanguage = $params['baseLanguage'] ?? null;

        $name = $attributeDefinition->getName();
        $description = $attributeDefinition->getDescription();
        if ($baseLanguage && $language) {
            $attributeDefinitionInBaseLanguage = $this->attributeDefinitionService->getAttributeDefinition(
                $attributeDefinition->getIdentifier(),
                [$baseLanguage->languageCode]
            );

            $name = $attributeDefinitionInBaseLanguage->getName();
            $description = $attributeDefinitionInBaseLanguage->getDescription();
        }

        return new AttributeDefinitionUpdateData(
            $attributeDefinition->getIdentifier(),
            $attributeDefinition->getIdentifier(),
            $attributeDefinition->getGroup(),
            $name,
            $description,
            $language,
            $attributeDefinition->getPosition(),
            $attributeDefinition->getOptions()->all()
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
