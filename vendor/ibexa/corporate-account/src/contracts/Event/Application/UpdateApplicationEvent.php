<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Event\Application;

use Ibexa\Contracts\CorporateAccount\Values\Application;
use Ibexa\Contracts\CorporateAccount\Values\ApplicationUpdateStruct;
use Symfony\Contracts\EventDispatcher\Event;

final class UpdateApplicationEvent extends Event
{
    private Application $updatedApplication;

    private Application $application;

    private ApplicationUpdateStruct $applicationUpdateStruct;

    /** @var string[]|null */
    private ?array $fieldIdentifiersToValidate;

    /**
     * @param string[]|null $fieldIdentifiersToValidate
     */
    public function __construct(
        Application $updatedApplication,
        Application $application,
        ApplicationUpdateStruct $applicationUpdateStruct,
        ?array $fieldIdentifiersToValidate = null
    ) {
        $this->updatedApplication = $updatedApplication;
        $this->application = $application;
        $this->applicationUpdateStruct = $applicationUpdateStruct;
        $this->fieldIdentifiersToValidate = $fieldIdentifiersToValidate;
    }

    public function getUpdatedApplication(): Application
    {
        return $this->updatedApplication;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function getApplicationUpdateStruct(): ApplicationUpdateStruct
    {
        return $this->applicationUpdateStruct;
    }

    /**
     * @return string[]|null
     */
    public function getFieldIdentifiersToValidate(): ?array
    {
        return $this->fieldIdentifiersToValidate;
    }
}
