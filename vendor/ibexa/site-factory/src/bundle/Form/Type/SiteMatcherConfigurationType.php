<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Form\Type;

use Ibexa\Bundle\SiteFactory\Form\Data\SiteMatcherConfigurationData;
use Ibexa\Bundle\SiteFactory\Form\DataTransformer\DomainNameTransformer;
use Ibexa\Bundle\SiteFactory\Validator\Constraints\HostnameWithOptionalPort;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

final class SiteMatcherConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('host', TextType::class, [
            'required' => true,
            'constraints' => [
                new NotBlank(),
                new HostnameWithOptionalPort(),
            ],
        ])
            ->addModelTransformer(new DomainNameTransformer());

        $builder->add('path', TextType::class, [
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SiteMatcherConfigurationData::class,
        ]);
    }
}

class_alias(SiteMatcherConfigurationType::class, 'EzSystems\EzPlatformSiteFactoryBundle\Form\Type\SiteMatcherConfigurationType');
