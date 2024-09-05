<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\AdminUi\Form\Type\ChoiceList\Loader\LanguageChoiceLoader;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class LanguageChoiceType extends AbstractType
{
    private LanguageService $languageService;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        LanguageService $languageService,
        ConfigResolverInterface $configResolver
    ) {
        $this->languageService = $languageService;
        $this->configResolver = $configResolver;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choice_loader' => ChoiceList::loader(
                $this,
                new LanguageChoiceLoader($this->languageService, $this->configResolver)
            ),
            'choice_label' => 'name',
            'choice_name' => 'languageCode',
            'choice_value' => 'languageCode',
        ]);
    }
}
