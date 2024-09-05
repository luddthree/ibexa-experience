<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\ParamConverter;

use Ibexa\Contracts\CorporateAccount\Service\ApplicationService;
use Ibexa\Contracts\CorporateAccount\Values\Application;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class ApplicationParamConverter implements ParamConverterInterface
{
    public const PARAMETER_APPLICATION = 'application';
    public const PARAMETER_APPLICATION_ID = 'applicationId';

    private ApplicationService $applicationService;

    public function __construct(
        ApplicationService $applicationService
    ) {
        $this->applicationService = $applicationService;
    }

    public function apply(
        Request $request,
        ParamConverter $configuration
    ): bool {
        $applicationId = $request->get(self::PARAMETER_APPLICATION_ID);

        if (null === $applicationId) {
            return false;
        }

        $application = $this->applicationService->getApplication((int)$applicationId);

        $request->attributes->set($configuration->getName(), $application);

        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return Application::class === $configuration->getClass()
            && self::PARAMETER_APPLICATION === $configuration->getName();
    }
}
