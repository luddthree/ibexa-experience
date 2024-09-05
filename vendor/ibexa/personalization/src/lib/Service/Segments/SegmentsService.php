<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Segments;

use Ibexa\Personalization\Factory\Segments\SegmentsStructFactoryInterface;
use Ibexa\Personalization\Value\Model\SegmentsStruct;

/**
 * @internal
 */
final class SegmentsService implements SegmentsServiceInterface
{
    private SegmentsStructFactoryInterface $segmentsStructFactory;

    public function __construct(SegmentsStructFactoryInterface $segmentsStructFactory)
    {
        $this->segmentsStructFactory = $segmentsStructFactory;
    }

    public function getSegmentsStruct(array $responseContents): SegmentsStruct
    {
        return $this->segmentsStructFactory->createSegmentsStruct($responseContents);
    }
}
