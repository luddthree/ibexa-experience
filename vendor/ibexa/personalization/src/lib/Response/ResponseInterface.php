<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Response;

use Ibexa\Contracts\Personalization\Value\ItemListInterface;
use Ibexa\Contracts\Rest\Output\Generator;

/**
 * @internal
 */
interface ResponseInterface
{
    public function render(Generator $generator, ItemListInterface $itemList): Generator;
}

class_alias(ResponseInterface::class, 'EzSystems\EzRecommendationClient\Response\ResponseInterface');
