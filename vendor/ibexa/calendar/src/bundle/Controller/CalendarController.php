<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Calendar\Controller;

use Ibexa\Bundle\Calendar\UI\CalendarConfigProvider;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CalendarController extends Controller
{
    /** @var \Ibexa\Bundle\Calendar\UI\CalendarConfigProvider */
    private $configProvider;

    public function __construct(CalendarConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    public function viewAction(Request $request): Response
    {
        return $this->render('@ibexadesign/calendar/view.html.twig', [
            'config' => $this->configProvider->getConfig(),
        ]);
    }
}

class_alias(CalendarController::class, 'EzSystems\EzPlatformCalendarBundle\Controller\CalendarController');
