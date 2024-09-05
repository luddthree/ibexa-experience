<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\ModelBuild;

use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
interface ModelBuildDataSenderInterface
{
    public function triggerModelBuild(
        int $customerId,
        string $licenseKey,
        string $modelId
    ): ResponseInterface;
}
