<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Type\Application;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\CorporateAccount\Form\ChoiceLoader\ApplicationStateChoiceLoader;
use Ibexa\CorporateAccount\Form\Data\Application\ApplicationSearchQueryData;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationSearchType extends AbstractType
{
    private ConfigResolverInterface $configResolver;

    public function __construct(
        ConfigResolverInterface $configResolver
    ) {
        $this->configResolver = $configResolver;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('query', TextType::class, [
                'required' => false,
            ])
            ->add('state', ChoiceType::class, [
                'choice_loader' => new ApplicationStateChoiceLoader(
                    $this->configResolver
                ),
                'choice_translation_domain' => 'ibexa_corporate_account_applications',
                'label' => /** @Desc("State") */ 'application.state',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ApplicationSearchQueryData::class,
            'translation_domain' => 'ibexa_corporate_account',
        ]);
    }
}
