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
use UnexpectedValueException;

final class BeforeCreateApplicationEvent extends Event
{
    private ApplicationCreateStruct $applicationCreateStruct;

    /** @var string[]|null */
    private ?array $fieldIdentifiersToValidate;

    private ?Application $application = null;

    /** @param string[]|null $fieldIdentifiersToValidate  */
    public function __construct(
        ApplicationCreateStruct $applicationCreateStruct,
        ?array $fieldIdentifiersToValidate = null
    ) {
        $this->applicationCreateStruct = $applicationCreateStruct;
        $this->fieldIdentifiersToValidate = $fieldIdentifiersToValidate;
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

    public function getApplication(): Application
    {
        if (!$this->hasApplication()) {
            throw new UnexpectedValueException(
                sprintf(
                    'Return value is not set or not of type %s. '
                    . 'Check hasApplication() or set it using setApplication() '
                    . 'before you call the getter.',
                    Application::class
                )
            );
        }

        return $this->application;
    }

    public function setApplication(?Application $application): void
    {
        $this->application = $application;
    }

    public function hasApplication(): bool
    {
        return $this->application instanceof Application;
    }
}
