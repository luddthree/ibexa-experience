<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\ModelBuild;

use Ibexa\Personalization\Value\ModelBuild\BuildReport;
use Ibexa\Personalization\Value\ModelBuild\ModelBuildStatus;

/**
 * @internal
 */
interface ModelBuildServiceInterface
{
    public function triggerModelBuild(
        int $customerId,
        string $referenceCode
    ): ?BuildReport;

    public function getModelBuildStatus(
        int $customerId,
        string $referenceCode,
        int $lastBuildsNumber = 1
    ): ?ModelBuildStatus;
}
