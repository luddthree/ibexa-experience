<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Form\Type;

use Ibexa\Bundle\SiteFactory\Form\Data\PublicAccessUpdateData;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PublicAccessUpdateType extends AbstractType
{
    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    public function __construct(
        PermissionResolver $permissionResolver
    ) {
        $this->permissionResolver = $permissionResolver;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('matcherConfiguration', SiteMatcherConfigurationType::class, [
            'label' => /** @Desc("Site Access Matching") */ 'matcher_configuration.label',
            'translation_domain' => 'ibexa_site_factory_forms',
        ]);

        $builder->add('config', SiteConfigType::class);
        if ($this->permissionResolver->hasAccess('site', 'change_status')) {
            $builder->add('status', ChoiceType::class, [
                'choices' => [
                    PublicAccess::STATUS_OFFLINE => PublicAccess::STATUS_OFFLINE,
                    PublicAccess::STATUS_ONLINE => PublicAccess::STATUS_ONLINE,
                ],
                'expanded' => true,
                'multiple' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PublicAccessUpdateData::class,
        ]);
    }
}

class_alias(PublicAccessUpdateType::class, 'EzSystems\EzPlatformSiteFactoryBundle\Form\Type\PublicAccessUpdateType');
