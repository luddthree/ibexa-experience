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
use UnexpectedValueException;

final class BeforeUpdateApplicationEvent extends Event
{
    private Application $application;

    private ApplicationUpdateStruct $applicationUpdateStruct;

    /** @var string[]|null */
    private ?array $fieldIdentifiersToValidate;

    private ?Application $updatedApplication = null;

    /**
     * @param string[]|null $fieldIdentifiersToValidate
     */
    public function __construct(
        Application $application,
        ApplicationUpdateStruct $applicationUpdateStruct,
        ?array $fieldIdentifiersToValidate = null
    ) {
        $this->application = $application;
        $this->applicationUpdateStruct = $applicationUpdateStruct;
        $this->fieldIdentifiersToValidate = $fieldIdentifiersToValidate;
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

    public function getUpdatedApplication(): Application
    {
        if (!$this->hasUpdatedApplication()) {
            throw new UnexpectedValueException(
                sprintf(
                    'Return value is not set or not of type %s. '
                    . 'Check hasUpdatedApplication() or set it using setUpdatedApplication() '
                    . 'before you call the getter.',
                    Application::class
                )
            );
        }

        return $this->updatedApplication;
    }

    public function setUpdatedApplication(?Application $updatedApplication): void
    {
        $this->updatedApplication = $updatedApplication;
    }

    public function hasUpdatedApplication(): bool
    {
        return $this->updatedApplication instanceof Application;
    }
}
