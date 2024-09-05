<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Model;

use Ibexa\Personalization\Value\Model\EditorContentList;

interface UpdateEditorListDataSenderInterface
{
    public function sendUpdateEditorList(
        int $customerId,
        string $licenseKey,
        string $referenceCode,
        EditorContentList $submodels
    ): void;
}

class_alias(UpdateEditorListDataSenderInterface::class, 'Ibexa\Platform\Personalization\Client\Consumer\Model\UpdateEditorListDataSenderInterface');
