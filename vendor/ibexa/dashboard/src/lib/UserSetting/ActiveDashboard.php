<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\UserSetting;

use Ibexa\Bundle\Dashboard\Form\Type\DashboardChoiceType;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\User\UserSetting\FormMapperInterface;
use Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ActiveDashboard implements ValueDefinitionInterface, FormMapperInterface
{
    public const IDENTIFIER = 'active_dashboard';

    private TranslatorInterface $translator;

    private LocationService $locationService;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        ConfigResolverInterface $configResolver,
        TranslatorInterface $translator,
        LocationService $locationService
    ) {
        $this->configResolver = $configResolver;
        $this->translator = $translator;
        $this->locationService = $locationService;
    }

    public function getName(): string
    {
        return $this->translator->trans(
            /** @Desc("Active dashboard") */
            'user.setting.active_dashboard.name',
            [],
            'ibexa_dashboard'
        );
    }

    public function getDescription(): string
    {
        return $this->translator->trans(
            /** @Desc("Active dashboard") */
            'user.setting.active_dashboard.description',
            [],
            'ibexa_dashboard'
        );
    }

    public function getDisplayValue(string $storageValue): string
    {
        try {
            return $this->locationService->loadLocationByRemoteId($storageValue)->getContent()->getName() ?? '';
        } catch (NotFoundException $e) {
            return '';
        }
    }

    public function getDefaultValue(): string
    {
        return $this->configResolver->getParameter('dashboard.default_dashboard_remote_id');
    }

    public function mapFieldForm(
        FormBuilderInterface $formBuilder,
        ValueDefinitionInterface $value
    ): FormBuilderInterface {
        return $formBuilder->create(
            'value',
            DashboardChoiceType::class,
            [
                'label' => 'user.setting.active_dashboard.name',
                'expanded' => true,
                'multiple' => false,
                'translation_domain' => 'ibexa_dashboard',
            ]
        );
    }
}
