<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Factory\Segments;

use Ibexa\Personalization\Form\Data\ModelData;
use Ibexa\Personalization\Value\Model\SegmentsStruct;
use Ibexa\Personalization\Value\Model\SegmentsUpdateStruct;

interface SegmentsUpdateStructFactoryInterface
{
    /**
     * @throws \Ibexa\Personalization\Exception\InvalidArgumentException
     */
    public function createFromModelData(ModelData $data, SegmentsStruct $segmentsStruct): SegmentsUpdateStruct;
}
