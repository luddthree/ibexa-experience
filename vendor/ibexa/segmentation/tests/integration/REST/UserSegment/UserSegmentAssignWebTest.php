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

/**
 * @phpstan-type THeaders array<string, string>
 * @phpstan-type TRequestContent string
 */
final class UserSegmentAssignWebTest extends AbstractWebTestCase
{
    private const METHOD = 'POST';
    private const URI = '/api/ibexa/v2/user/users/14/segments';

    /**
     * @phpstan-param THeaders $headers
     * @phpstan-param TRequestContent $requestPayload
     *
     * @dataProvider provideForTestPayload
     */
    public function testPayload(array $headers, string $requestPayload): void
    {
        self::assertSegmentAssignmentCount(
            1,
            'Sanity check failed. Expected fixture assignment entry to be reachable.'
        );

        self::assertClientJsonRequest(
            $this->client,
            self::METHOD,
            self::URI,
            $headers,
            $requestPayload
        );

        self::assertSegmentAssignmentCount(2);
    }

    /**
     * @return iterable<string, array{array<string, string>, string}>
     */
    public function provideForTestPayload(): iterable
    {
        yield 'json' => [
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.UserSegmentAssignInput+json',
            ],
            <<<JSON
                {
                    "UserSegmentAssignInput": {
                        "segments": ["segment_test_bar"]
                    }
                }
            JSON,
        ];

        yield 'xml' => [
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.UserSegmentAssignInput+xml',
            ],
            <<<XML
                <UserSegmentAssignInput>
                    <segments>segment_test_bar</segments>
                </UserSegmentAssignInput>
            XML,
        ];
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
