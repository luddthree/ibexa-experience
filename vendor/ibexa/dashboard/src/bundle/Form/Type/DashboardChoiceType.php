<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Dashboard\Form\Type;

use DateTimeInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\UserPreferenceService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DashboardChoiceType extends DashboardAbstractType
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
            'choice_loader' => ChoiceList::lazy($this, $this->getChoiceLoader()),
            'translation_domain' => 'ibexa_dashboard',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * @phpstan-return callable(): array<string, string>
     */
    private function getChoiceLoader(): callable
    {
        return function (): array {
            $choices = [];
            $defaultDashboardRemoteId = $this->configResolver->getParameter(
                'dashboard.default_dashboard_remote_id'
            );
            try {
                $defaultDashboard = $this->locationService->loadLocationByRemoteId($defaultDashboardRemoteId);
                $choices[$defaultDashboard->getContent()->getName()] = $defaultDashboard->remoteId;
            } catch (NotFoundException|UnauthorizedException $e) {
                // Do nothing
            }

            foreach ($this->getUserCustomDashboards() as $userCustomDashboard) {
                $choices[$this->getDashboardName($choices, $userCustomDashboard)] = $userCustomDashboard->remoteId;
            }

            return $choices;
        };
    }

    /**
     * @param array<string, string> $choices
     */
    private function getDashboardName(array $choices, Location $userCustomDashboard): string
    {
        $name = $userCustomDashboard->getContent()->getName() ?? '';

        if (array_key_exists($name, $choices)) {
            return sprintf(
                '%s (%s)',
                $name,
                $userCustomDashboard->getContentInfo()->publishedDate->format(DateTimeInterface::RFC850)
            );
        }

        return $name;
    }
}
