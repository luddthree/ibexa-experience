<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Model;

use Ibexa\Personalization\Value\Model\SegmentsUpdateStruct;

interface UpdateSegmentsDataSenderInterface
{
    public function sendUpdateSegments(
        int $customerId,
        string $licenseKey,
        string $referenceCode,
        SegmentsUpdateStruct $segmentsUpdateStruct
    ): void;
}
