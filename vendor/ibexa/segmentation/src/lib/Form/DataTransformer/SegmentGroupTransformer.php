<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Form\DataTransformer;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Segmentation\Value\SegmentGroup;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class SegmentGroupTransformer implements DataTransformerInterface
{
    /** @var \Ibexa\Contracts\Segmentation\SegmentationServiceInterface */
    private $segmentationService;

    public function __construct(SegmentationServiceInterface $segmentationService)
    {
        $this->segmentationService = $segmentationService;
    }

    public function transform($value): ?int
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof SegmentGroup) {
            throw new TransformationFailedException('Expected a ' . SegmentGroup::class . ' object.');
        }

        return $value->id;
    }

    public function reverseTransform($value): ?SegmentGroup
    {
        if (empty($value)) {
            return null;
        }

        if (!is_int($value) && !ctype_digit($value)) {
            throw new TransformationFailedException('Expected a numeric string.');
        }

        try {
            return $this->segmentationService->loadSegmentGroup((int) $value);
        } catch (NotFoundException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}

class_alias(SegmentGroupTransformer::class, 'Ibexa\Platform\Segmentation\Form\DataTransformer\SegmentGroupTransformer');
