<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Model;

use Ibexa\Personalization\Value\Model\SubmodelList;

interface UpdateSubmodelsDataSenderInterface
{
    public function sendUpdateSubmodels(
        int $customerId,
        string $licenseKey,
        string $referenceCode,
        SubmodelList $submodels
    ): void;
}

class_alias(UpdateSubmodelsDataSenderInterface::class, 'Ibexa\Platform\Personalization\Client\Consumer\Model\UpdateSubmodelsDataSenderInterface');
