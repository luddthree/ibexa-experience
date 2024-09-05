<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Attribute;

use Ibexa\AdminUi\Form\Type\ChoiceList\Loader\BaseChoiceLoader;
use Ibexa\Contracts\Core\Repository\LanguageResolver;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;

final class SelectionValueChoiceLoader extends BaseChoiceLoader
{
    private LanguageResolver $languageResolver;

    private AttributeDefinitionInterface $attributeDefinition;

    public function __construct(LanguageResolver $languageResolver, AttributeDefinitionInterface $attributeDefinition)
    {
        $this->languageResolver = $languageResolver;
        $this->attributeDefinition = $attributeDefinition;
    }

    /**
     * @return array<string,mixed>
     */
    public function getChoiceList(): array
    {
        $languages = $this->languageResolver->getPrioritizedLanguages();

        /** @var array<array{ label: array<string,string>, value: string }> $options */
        $options = $this->attributeDefinition->getOptions()->get('choices', []);

        $choices = [];
        foreach ($options as $option) {
            $label = $option['value'];
            foreach ($languages as $language) {
                if (isset($option['label'][$language])) {
                    $label = $option['label'][$language];
                    break;
                }
            }

            $choices[$label] = $option['value'];
        }

        return $choices;
    }
}
