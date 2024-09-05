<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Form\Type;

use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SiteStatusChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => [
                /** @Desc("Online") */
                'choices.online' => Site::STATUS_ONLINE,
                /** @Desc("Offline") */
                'choices.offline' => Site::STATUS_OFFLINE,
            ],
            'translation_domain' => 'ibexa_site_factory_forms',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}

class_alias(SiteStatusChoiceType::class, 'EzSystems\EzPlatformSiteFactoryBundle\Form\Type\SiteStatusChoiceType');
