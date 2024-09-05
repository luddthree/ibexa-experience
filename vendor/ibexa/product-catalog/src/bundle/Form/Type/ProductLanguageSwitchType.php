<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Closure;
use Ibexa\Bundle\ProductCatalog\Form\Data\ProductLanguageSwitchData;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductLanguageSwitchType extends AbstractType
{
    private ConfigResolverInterface $configResolver;

    public function __construct(
        ConfigResolverInterface $configResolver
    ) {
        $this->configResolver = $configResolver;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductLanguageSwitchData::class,
            'languages' => Closure::fromCallable([$this, 'getContentLanguages']),
        ]);

        $resolver->setRequired('product');
        $resolver->setAllowedTypes('product', ContentAwareProductInterface::class);
    }

    /**
     * If content is requested without specifying a language,
     * the content will be displayed in the first Site Access language
     * that is also available as a translation.
     * We need to ensure that the same language is also used as first select option.
     *
     * @return array<string>
     */
    private function getContentLanguages(Options $options): array
    {
        $contentLanguages = $options['product']->getContent()->versionInfo->languageCodes;
        $languagesByCode = [];

        foreach ($contentLanguages as $language) {
            $languagesByCode[$language] = $language;
        }

        $saLanguages = [];
        // prioritize Site Access languages with available translation
        foreach ($this->configResolver->getParameter('languages') as $languageCode) {
            if (!isset($languagesByCode[$languageCode])) {
                continue;
            }

            $saLanguages[] = $languagesByCode[$languageCode];
            unset($languagesByCode[$languageCode]);
        }

        return array_merge($saLanguages, array_values($languagesByCode));
    }

    public function getParent(): string
    {
        return LanguageSwitchType::class;
    }
}
