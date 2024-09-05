<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Form\Type;

use Ibexa\Bundle\SiteFactory\Form\DataTransformer\SiteTransformer;
use Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SiteType extends AbstractType
{
    /** @var \Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface */
    protected $siteService;

    public function __construct(SiteServiceInterface $siteService)
    {
        $this->siteService = $siteService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new SiteTransformer($this->siteService));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('multiple', false);
        $resolver->setRequired(['multiple']);
        $resolver->setAllowedTypes('multiple', 'boolean');
    }

    public function getParent()
    {
        return HiddenType::class;
    }
}

class_alias(SiteType::class, 'EzSystems\EzPlatformSiteFactoryBundle\Form\Type\SiteType');
