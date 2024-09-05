<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\REST;

use Ibexa\Bundle\ProductCatalog\REST\Value\RegionView;
use Ibexa\Contracts\ProductCatalog\RegionServiceInterface;
use Ibexa\Rest\Message;
use Ibexa\Rest\Server\Controller as RestController;
use Ibexa\Rest\Value;
use Symfony\Component\HttpFoundation\Request;

final class RegionViewController extends RestController
{
    private RegionServiceInterface $regionService;

    public function __construct(RegionServiceInterface $regionService)
    {
        $this->regionService = $regionService;
    }

    public function createView(Request $request): Value
    {
        $viewInput = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        return new RegionView(
            $viewInput->identifier,
            $this->regionService->findRegions($viewInput->query)
        );
    }
}
