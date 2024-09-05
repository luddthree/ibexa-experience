<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Form\Type;

use Ibexa\Bundle\SiteFactory\Form\Data\PublicAccessData;
use Ibexa\Bundle\SiteFactory\Form\Data\SiteCreateData;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SiteCreateType extends AbstractType
{
    public const BTN_SAVE = 'save';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('design', DesignChoiceType::class);
        $builder->add('copySiteSkeleton', CheckboxType::class, [
            'label' => /** @Desc("Generate site using site skeleton") */ 'copy_site_skeleton.label',
            'required' => false,
        ]);
        $builder->add('parentLocationId', SiteParentLocationType::class, [
            'label' => /** @Desc("Parent Location") */ 'parent_location.label',
            'required' => true,
        ]);
        $builder->add('siteName', TextType::class, [
            'label' => /** @Desc("Name") */ 'name.label',
        ]);
        $builder->add('publicAccesses', CollectionType::class, [
            'entry_type' => PublicAccessType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'prototype_data' => new PublicAccessData(),
        ])
            ->add(self::BTN_SAVE, SubmitType::class, [
                'label' => /** @Desc("Save") */ 'object_state.update.save',
            ])
            ->add('save_and_close', SubmitType::class, [
                'label' => /** @Desc("Save and close") */ 'object_state.update.save_and_close',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SiteCreateData::class,
            'translation_domain' => 'ibexa_site_factory_forms',
        ]);
    }
}

class_alias(SiteCreateType::class, 'EzSystems\EzPlatformSiteFactoryBundle\Form\Type\SiteCreateType');
