<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Data\Application;

final class ApplicationsDeleteData
{
    /** @var array<int, bool> */
    private array $applications;

    /**
     * @param array<int, bool> $applications
     */
    public function __construct(array $applications = [])
    {
        $this->applications = $applications;
    }

    /**
     * @return array<int, bool>
     */
    public function getApplications(): array
    {
        return $this->applications;
    }

    /**
     * @param array<int, bool> $applications
     */
    public function setApplications(array $applications): void
    {
        $this->applications = $applications;
    }
}
