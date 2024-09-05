<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SiteAccessGroupChoiceType extends AbstractType
{
    /**
     * @var string[]
     */
    private $siteAccessGroups;

    public function __construct(array $siteAccessGroups)
    {
        $this->siteAccessGroups = array_keys($siteAccessGroups);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => array_combine($this->siteAccessGroups, $this->siteAccessGroups),
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}

class_alias(SiteAccessGroupChoiceType::class, 'EzSystems\EzPlatformSiteFactoryBundle\Form\Type\SiteAccessGroupChoiceType');
