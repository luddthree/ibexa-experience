<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Form\Type;

use Ibexa\Bundle\SiteFactory\Form\Data\SiteDeleteData;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SiteDeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'site',
                SiteType::class,
                ['label' => false, 'attr' => ['hidden' => true]]
            )
            ->add(
                'delete',
                SubmitType::class,
                [
                    'label' => /** @Desc("Delete") */
                        'site_delete_form.delete',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SiteDeleteData::class,
            'translation_domain' => 'ibexa_site_factory_forms',
        ]);
    }
}

class_alias(SiteDeleteType::class, 'EzSystems\EzPlatformSiteFactoryBundle\Form\Type\SiteDeleteType');
