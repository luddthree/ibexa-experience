<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Dashboard\Form\Type;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\UserPreferenceService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AvailableDashboardType extends DashboardAbstractType
{
    private ConfigResolverInterface $configResolver;

    public function __construct(
        LocationService $locationService,
        ConfigResolverInterface $configResolver,
        UserPreferenceService $userPreferenceService
    ) {
        parent::__construct($locationService, $userPreferenceService);

        $this->configResolver = $configResolver;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choice_loader' => ChoiceList::lazy($this, $this->getDashboardLocations()),
            'choice_filter' => static function (Options $options): callable {
                /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $currentDashboard */
                $currentDashboard = $options['current_dashboard'];

                return static function (?Location $location = null) use ($currentDashboard): bool {
                    return null !== $location && $location->remoteId !== $currentDashboard->remoteId;
                };
            },
            'choice_value' => ChoiceList::value(
                $this,
                static fn (Location $location): string => $location->remoteId
            ),
            'choice_label' => ChoiceList::label(
                $this,
                static fn (Location $location): string => $location->getContent()->getName() ?? ''
            ),
            'translation_domain' => 'ibexa_dashboard',
        ]);

        $resolver->setRequired(['current_dashboard']);
        $resolver->setAllowedTypes('current_dashboard', [Location::class, 'null']);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * @phpstan-return callable(): array<\Ibexa\Contracts\Core\Repository\Values\Content\Location>
     */
    private function getDashboardLocations(): callable
    {
        return function (): array {
            $locations = [];
            $defaultDashboardRemoteId = $this->configResolver->getParameter(
                'dashboard.default_dashboard_remote_id'
            );
            try {
                $defaultDashboard = $this->locationService->loadLocationByRemoteId($defaultDashboardRemoteId);
                $locations[] = $defaultDashboard;
            } catch (NotFoundException|UnauthorizedException $e) {
                // Do nothing
            }

            foreach ($this->getUserCustomDashboards() as $userCustomDashboard) {
                $locations[] = $userCustomDashboard;
            }

            return $locations;
        };
    }
}
