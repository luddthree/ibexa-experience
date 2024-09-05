<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Platform\Segmentation\Tests\integration\REST\UserSegment;

use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Tests\Integration\Segmentation\AbstractWebTestCase;

final class UserSegmentUnassignWebTest extends AbstractWebTestCase
{
    private const METHOD = 'DELETE';
    private const URI = '/api/ibexa/v2/user/users/14/segments/segment_test_foo';

    public function testGettingUserSegments(): void
    {
        self::assertSegmentAssignmentCount(
            1,
            'Sanity check failed. Expected fixture assignment entry to be reachable.'
        );

        self::assertClientJsonRequest(
            $this->client,
            self::METHOD,
            self::URI,
            [
                'HTTP_ACCEPT' => 'application/json',
            ],
        );

        self::assertSegmentAssignmentCount(0);
    }

    protected static function getResourceType(): ?string
    {
        return null;
    }

    private static function assertSegmentAssignmentCount(int $expectedCount, string $message = ''): void
    {
        $segmentationService = self::getServiceByClassName(SegmentationServiceInterface::class);
        $userService = self::getServiceByClassName(UserService::class);
        $segmentsAssigned = $segmentationService->loadSegmentsAssignedToUser($userService->loadUser(14));

        self::assertCount($expectedCount, $segmentsAssigned, $message);
    }
}
