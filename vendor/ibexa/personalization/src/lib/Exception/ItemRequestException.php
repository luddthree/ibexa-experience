<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Exception;

use Ibexa\Personalization\Request\Item\ItemRequest;

final class ItemRequestException extends TransferException
{
    /**
     * @throws \JsonException
     */
    public function __construct(
        string $action,
        ItemRequest $itemRequest,
        BadResponseException $badResponseException
    ) {
        $message = sprintf(
            'Could not trigger %s with: %s. Error message: %s',
            $action,
            json_encode($itemRequest, JSON_THROW_ON_ERROR),
            $badResponseException->getMessage()
        );

        parent::__construct($message);
    }
}
