<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ActivityLog\REST;

use Ibexa\Contracts\Test\Rest\BaseRestWebTestCase;
use Ibexa\Contracts\Test\Rest\Input\PayloadLoader;
use Ibexa\Contracts\Test\Rest\Request\Value\EndpointRequestDefinition;

final class ActivityLogQueryWebTest extends BaseRestWebTestCase
{
    private const URI = '/api/ibexa/v2/activity-log-group/list';
    private const INPUT_MEDIA_TYPE = 'ActivityLogGroupListInput';
    private const RESOURCE_TYPE = 'ActivityLogGroupList';

    protected static function getEndpointsToTest(): iterable
    {
        $payloadLoader = new PayloadLoader(dirname(__DIR__) . '/Resources/REST/InputPayloads');
        $endpointRequestDefinition = new EndpointRequestDefinition(
            'POST',
            self::URI,
            self::RESOURCE_TYPE,
        );

        yield from self::provideDifferentAcceptFormats(
            ...self::provideDifferentPayloadFormats(
                $payloadLoader,
                'ActivityLogGroupListInput',
                $endpointRequestDefinition->withSnapshotName('ActivityLogGroupList'),
            ),
            ...self::provideDifferentPayloadFormats(
                $payloadLoader,
                'ActivityLogGroupListInput.withCriteria',
                $endpointRequestDefinition->withSnapshotName('ActivityLogGroupList.withCriteria'),
            ),
            ...self::provideDifferentPayloadFormats(
                $payloadLoader,
                'ActivityLogGroupListInput.or',
                $endpointRequestDefinition->withSnapshotName('ActivityLogGroupList.or'),
            ),
            ...self::provideDifferentPayloadFormats(
                $payloadLoader,
                'ActivityLogGroupListInput.and',
                $endpointRequestDefinition->withSnapshotName('ActivityLogGroupList.and'),
            ),
            ...self::provideDifferentPayloadFormats(
                $payloadLoader,
                'ActivityLogGroupListInput.not',
                $endpointRequestDefinition->withSnapshotName('ActivityLogGroupList.not'),
            ),
            ...self::provideDifferentPayloadFormats(
                $payloadLoader,
                'ActivityLogGroupListInput.sortClauses',
                $endpointRequestDefinition->withSnapshotName('ActivityLogGroupList.sortClauses'),
            ),
        );

        yield from self::provideDifferentAcceptFormats(
            (new EndpointRequestDefinition(
                'GET',
                self::URI,
                self::RESOURCE_TYPE,
            ))->withSnapshotName('ActivityLogGroupList.GET'),
            (new EndpointRequestDefinition(
                'GET',
                self::URI . '?limit=2&offset=1',
                self::RESOURCE_TYPE,
            ))->withSnapshotName('ActivityLogGroupList.GET.limit_offset_only'),
            (new EndpointRequestDefinition(
                'GET',
                self::URI . '?limit=2&offset=1'
                . '&filter%5B0%5D%5Btype%5D=action&filter%5B0%5D%5Bvalue%5D=foo_action'
                . '&sort%5B0%5D%5Btype%5D=logged_at&sort%5B0%5D%5Bdirection%5D=DESC'
                . '&filter%5B1%5D%5Btype%5D=object_class&filter%5B1%5D%5Bclass%5D=stdClass',
                self::RESOURCE_TYPE,
            ))->withSnapshotName('ActivityLogGroupList.GET.withCriteria'),
        );
    }

    /**
     * @return iterable<\Ibexa\Contracts\Test\Rest\Request\Value\EndpointRequestDefinition>
     */
    private static function provideDifferentPayloadFormats(
        PayloadLoader $payloadLoader,
        string $payloadName,
        EndpointRequestDefinition ...$endpointRequestDefinitions
    ): iterable {
        foreach ($endpointRequestDefinitions as $endpointRequestDefinition) {
            foreach (self::REQUIRED_FORMATS as $format) {
                $payload = $payloadLoader->loadPayload(self::INPUT_MEDIA_TYPE, $format, $payloadName);
                yield $endpointRequestDefinition->withPayload($payload);
            }
        }
    }

    /**
     * @return iterable<\Ibexa\Contracts\Test\Rest\Request\Value\EndpointRequestDefinition>
     */
    private static function provideDifferentAcceptFormats(
        EndpointRequestDefinition ...$endpointRequestDefinitions
    ): iterable {
        foreach ($endpointRequestDefinitions as $endpointRequestDefinition) {
            foreach (self::REQUIRED_FORMATS as $format) {
                $mediaType = self::generateMediaTypeString(self::RESOURCE_TYPE, $format);
                yield $endpointRequestDefinition->withAcceptHeader($mediaType);
            }
        }
    }

    protected function getResourceType(): string
    {
        return 'ActivityLogGroupList';
    }

    protected function getSchemaFileBasePath(string $resourceType, string $format): string
    {
        return dirname(__DIR__) . '/Resources/REST/Schemas/' . $resourceType;
    }

    protected static function getSnapshotDirectory(): string
    {
        return dirname(__DIR__) . '/Resources/REST/Snapshots';
    }
}
