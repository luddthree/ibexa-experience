<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Event\Application;

use Ibexa\Contracts\CorporateAccount\Values\Application;
use Symfony\Contracts\EventDispatcher\Event;

final class BeforeDeleteApplicationEvent extends Event
{
    private Application $application;

    public function __construct(
        Application $application
    ) {
        $this->application = $application;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }
}
