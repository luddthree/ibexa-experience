<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Form\Type;

use Ibexa\Bundle\SiteFactory\Form\DataTransformer\StringListDataTransformer;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class SiteConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            $builder->create('languages', TextType::class, [
                'label' => /** @Desc("Language(s)") */ 'languages.label',
                'translation_domain' => 'ibexa_site_factory_forms',
            ])->addModelTransformer(new StringListDataTransformer())
        );
    }
}

class_alias(SiteConfigType::class, 'EzSystems\EzPlatformSiteFactoryBundle\Form\Type\SiteConfigType');
