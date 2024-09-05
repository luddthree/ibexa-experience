<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Item;

use GuzzleHttp\Psr7\Uri;
use Ibexa\Contracts\Core\Repository\TokenService;
use Ibexa\Personalization\Client\Consumer\Item\ItemDataSenderInterface;
use Ibexa\Personalization\Request\Item\ItemRequest;
use Ibexa\Personalization\Request\Item\PackageList;
use Ibexa\Personalization\Value\Authentication\Parameters as AuthenticationParameters;
use Ibexa\Personalization\Value\Export\Parameters as ExportParameters;
use Ibexa\Personalization\Value\Token\Token;

/**
 * @internal
 */
final class ItemService implements ItemServiceInterface
{
    private ItemDataSenderInterface $itemDataSender;

    private TokenService $tokenService;

    private int $tokenTtl;

    public function __construct(
        ItemDataSenderInterface $itemDataSender,
        TokenService $tokenService,
        int $tokenTtl
    ) {
        $this->itemDataSender = $itemDataSender;
        $this->tokenService = $tokenService;
        $this->tokenTtl = $tokenTtl;
    }

    public function export(
        ExportParameters $parameters,
        PackageList $packageList
    ): void {
        $itemRequest = new ItemRequest(
            $packageList,
            $this->generateToken(Token::IDENTIFIER_EXPORT),
            ItemRequest::DEFAULT_HEADERS
        );

        $this->itemDataSender->triggerExport(
            new AuthenticationParameters(
                (int)$parameters->getCustomerId(),
                $parameters->getLicenseKey()
            ),
            $itemRequest,
            new Uri($parameters->getWebHook())
        );
    }

    public function update(
        AuthenticationParameters $parameters,
        PackageList $packageList
    ): void {
        $itemRequest = new ItemRequest(
            $packageList,
            $this->generateToken(Token::IDENTIFIER_UPDATE),
            ItemRequest::DEFAULT_HEADERS
        );

        $this->itemDataSender->triggerUpdate($parameters, $itemRequest);
    }

    public function delete(
        AuthenticationParameters $parameters,
        PackageList $packageList
    ): void {
        $this->itemDataSender->triggerDelete(
            $parameters,
            new ItemRequest($packageList)
        );
    }

    /**
     * @phpstan-param \Ibexa\Personalization\Value\Token\Token::IDENTIFIER_* $identifier
     */
    private function generateToken(string $identifier): string
    {
        $token = $this->tokenService->generateToken(
            Token::TYPE,
            $this->tokenTtl,
            $identifier
        );

        return $token->getToken();
    }
}
