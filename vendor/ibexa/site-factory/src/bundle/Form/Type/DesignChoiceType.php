<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Form\Type;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\SiteFactory\Values\Design\Template;
use Ibexa\SiteFactory\DesignRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DesignChoiceType extends AbstractType
{
    /** @var \Ibexa\SiteFactory\DesignRegistry */
    private $designRegistry;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    public function __construct(
        DesignRegistry $designRegistry,
        LocationService $locationService
    ) {
        $this->designRegistry = $designRegistry;
        $this->locationService = $locationService;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choices' => $this->designRegistry->getTemplates(),
                'choice_label' => static function (?Template $template): string {
                    return $template->name;
                },
                'choice_value' => static function (?Template $template): ?string {
                    return $template->identifier ?? null;
                },
                'choice_attr' => function (?Template $template): array {
                    return [
                        'data-thumbnail' => $template->thumbnail,
                        'data-site-skeleton' => $template->getSiteSkeletonLocation()->id ?? null,
                        'data-parent-location' => $template->getParentLocation()->id ?? null,
                        'data-parent-location-breadcrumbs' => $template->getParentLocation() !== null
                            ? $this->getBreadcrumbs($template->getParentLocation())
                            : null,
                    ];
                },
            ]);
    }

    private function getBreadcrumbs(Location $location): string
    {
        $path = $location->path;
        array_shift($path);
        $locations = $this->locationService->loadLocationList($path);

        $breadcrumbs = [];
        foreach ($locations as $location) {
            $breadcrumbs[] = $location->getContent()->getName();
        }

        return implode(' / ', $breadcrumbs);
    }
}

class_alias(DesignChoiceType::class, 'EzSystems\EzPlatformSiteFactoryBundle\Form\Type\DesignChoiceType');
