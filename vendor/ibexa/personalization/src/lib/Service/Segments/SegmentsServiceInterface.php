<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Segments;

use Ibexa\Personalization\Value\Model\SegmentsStruct;

/**
 * @internal
 *
 * @phpstan-import-type T from \Ibexa\Personalization\Factory\Segments\SegmentsStructFactoryInterface as TResponseContents
 */
interface SegmentsServiceInterface
{
    /**
     * @phpstan-param TResponseContents $responseContents
     */
    public function getSegmentsStruct(array $responseContents): SegmentsStruct;
}
