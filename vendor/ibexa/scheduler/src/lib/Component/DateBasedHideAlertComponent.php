<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Component;

use Ibexa\Contracts\AdminUi\Component\Renderable;
use Ibexa\Contracts\Scheduler\Repository\DateBasedHideServiceInterface;
use Ibexa\Scheduler\Form\Data\DateBasedHideCancelData;
use Ibexa\Scheduler\Form\Type\DateBasedHideCancelType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Util\StringUtil;
use Twig\Environment;

final class DateBasedHideAlertComponent implements Renderable
{
    /** @var \Twig\Environment */
    private $twig;

    /** @var \Symfony\Component\Form\FormFactoryInterface */
    private $formFactory;

    /** @var \Ibexa\Contracts\Scheduler\Repository\DateBasedHideServiceInterface */
    private $dateBasedHideService;

    public function __construct(
        Environment $twig,
        FormFactoryInterface $formFactory,
        DateBasedHideServiceInterface $dateBasedHideService
    ) {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->dateBasedHideService = $dateBasedHideService;
    }

    public function render(array $parameters = []): string
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo $versionInfo */
        $versionInfo = $parameters['versionInfo'];
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location */
        $location = $parameters['location'];

        $isScheduledHide = $this->dateBasedHideService->isScheduledHide($location->contentId);

        if (!$isScheduledHide) {
            return '';
        }

        $scheduledEntry = $this->dateBasedHideService->getScheduledHide($location->contentId);

        $formView = $this->formFactory->createNamed(
            StringUtil::fqcnToBlockPrefix(DateBasedHideCancelType::class),
            DateBasedHideCancelType::class,
            new DateBasedHideCancelData($location, $versionInfo)
        )->createView();

        return $this->twig->render('@IbexaScheduler/content/scheduled_hide_alert.html.twig', [
            'scheduledDate' => $scheduledEntry->date,
            'form_date_based_hide_cancel' => $formView,
        ]);
    }
}

class_alias(DateBasedHideAlertComponent::class, 'EzSystems\DateBasedPublisher\Core\Component\DateBasedHideAlertComponent');
