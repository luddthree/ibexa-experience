<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Model;

use Psr\Http\Message\ResponseInterface;

interface EditorListDataFetcherInterface
{
    public function fetchEditorList(
        int $customerId,
        string $licenseKey,
        string $referenceCode
    ): ResponseInterface;
}

class_alias(EditorListDataFetcherInterface::class, 'Ibexa\Platform\Personalization\Client\Consumer\Model\EditorListDataFetcherInterface');
