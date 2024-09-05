<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\SiteFactory\Form\Type;

use Ibexa\Bundle\SiteFactory\Form\Data\SiteListData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SiteListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('searchQuery', TextType::class, [
            'required' => false,
        ]);

        $builder->add('page', HiddenType::class, [
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SiteListData::class,
        ]);
    }
}

class_alias(SiteListType::class, 'EzSystems\EzPlatformSiteFactoryBundle\Form\Type\SiteListType');
