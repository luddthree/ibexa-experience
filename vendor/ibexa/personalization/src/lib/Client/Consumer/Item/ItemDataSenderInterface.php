<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Item;

use Ibexa\Personalization\Request\Item\ItemRequest;
use Ibexa\Personalization\Value\Authentication\Parameters;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * @internal
 */
interface ItemDataSenderInterface
{
    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function triggerExport(
        Parameters $parameters,
        ItemRequest $request,
        ?UriInterface $webHookUri = null
    ): ResponseInterface;

    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function triggerUpdate(
        Parameters $parameters,
        ItemRequest $request
    ): ResponseInterface;

    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function triggerDelete(
        Parameters $parameters,
        ItemRequest $request
    ): ResponseInterface;
}
