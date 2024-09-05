<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Model;

use Ibexa\Personalization\Value\Model\Model;

interface UpdateModelDataSenderInterface
{
    public function sendUpdateModel(
        int $customerId,
        string $licenseKey,
        Model $model,
        array $properties = []
    ): void;
}

class_alias(UpdateModelDataSenderInterface::class, 'Ibexa\Platform\Personalization\Client\Consumer\Model\UpdateModelDataSenderInterface');
