<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Event\Application;

use Ibexa\Contracts\CorporateAccount\Values\Application;
use Ibexa\Contracts\CorporateAccount\Values\ApplicationCreateStruct;
use Symfony\Contracts\EventDispatcher\Event;

final class CreateApplicationEvent extends Event
{
    private Application $application;

    private ApplicationCreateStruct $applicationCreateStruct;

    /** @var string[]|null */
    private ?array $fieldIdentifiersToValidate;

    /**
     * @param string[]|null $fieldIdentifiersToValidate
     */
    public function __construct(
        Application $application,
        ApplicationCreateStruct $applicationCreateStruct,
        ?array $fieldIdentifiersToValidate = null
    ) {
        $this->application = $application;
        $this->applicationCreateStruct = $applicationCreateStruct;
        $this->fieldIdentifiersToValidate = $fieldIdentifiersToValidate;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function getApplicationCreateStruct(): ApplicationCreateStruct
    {
        return $this->applicationCreateStruct;
    }

    /**
     * @return string[]|null
     */
    public function getFieldIdentifiersToValidate(): ?array
    {
        return $this->fieldIdentifiersToValidate;
    }
}
