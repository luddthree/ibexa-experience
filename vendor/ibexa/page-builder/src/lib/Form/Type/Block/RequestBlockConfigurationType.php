<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Form\Type\Block;

use Ibexa\AdminUi\Form\Type\Content\ContentInfoType;
use Ibexa\AdminUi\Form\Type\Content\LocationType;
use Ibexa\AdminUi\Form\Type\Content\VersionInfoType;
use Ibexa\AdminUi\Form\Type\Language\LanguageType;
use Ibexa\PageBuilder\Data\RequestBlockConfiguration;
use Ibexa\PageBuilder\Form\Type\Page\PageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequestBlockConfigurationType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('location', LocationType::class)
            ->add('content_info', ContentInfoType::class)
            ->add('version_info', VersionInfoType::class)
            ->add('page', PageType::class)
            ->add('block_id', TextType::class)
            ->add('language', LanguageType::class)
            ->add('request', SubmitType::class)
        ;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', RequestBlockConfiguration::class);
    }
}

class_alias(RequestBlockConfigurationType::class, 'EzSystems\EzPlatformPageBuilder\Form\Type\Block\RequestBlockConfigurationType');
