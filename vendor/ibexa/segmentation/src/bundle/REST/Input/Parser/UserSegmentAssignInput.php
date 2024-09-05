<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\REST\Input\Parser;

use Ibexa\Bundle\Segmentation\REST\Input\UserSegmentAssignInput as UserSegmentAssignInputObject;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Rest\Input\BaseParser;

final class UserSegmentAssignInput extends BaseParser
{
    private const OBJECT_KEYS = [
        'segments',
    ];

    private SegmentationServiceInterface $segmentationService;

    public function __construct(SegmentationServiceInterface $segmentationService)
    {
        $this->segmentationService = $segmentationService;
    }

    /**
     * @param array{
     *     segments: string|string[],
     * } $data
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): UserSegmentAssignInputObject
    {
        $inputKeys = array_keys($data);
        $redundantKeys = array_diff($inputKeys, self::OBJECT_KEYS);

        if (!empty($redundantKeys)) {
            throw new Parser(
                sprintf(
                    'The following properties for UserSegmentAssignInput are redundant: %s.',
                    implode(', ', $redundantKeys)
                )
            );
        }

        $segments = [];
        if (is_string($data['segments'])) {
            $data['segments'] = [$data['segments']];
        }

        if (!is_array($data['segments'])) {
            throw new InvalidArgumentException('segments', 'expected an array');
        }

        foreach ($data['segments'] as $key => $segment) {
            if (!is_string($segment)) {
                throw new InvalidArgumentException("segments[$key]", 'expected a string');
            }
        }

        foreach ($data['segments'] as $segmentIdentifier) {
            $segments[] = $this->segmentationService->loadSegmentByIdentifier((string) $segmentIdentifier);
        }

        return new UserSegmentAssignInputObject($segments);
    }
}
