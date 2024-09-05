<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Serializer\Handler;

use Exception;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\FieldTypePage\ScheduleBlock\Item\UnavailableContentInfo;
use Ibexa\FieldTypePage\ScheduleBlock\Item\UnavailableLocation;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

class LocationHandler implements SubscribingHandlerInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /**
     * @param \Ibexa\Contracts\Core\Repository\LocationService $locationService
     */
    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public static function getSubscribingMethods(): array
    {
        return [
            [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => Location::class,
                'method' => 'serializeLocation',
            ],
            [
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'type' => Location::class,
                'method' => 'deserializeLocation',
            ],
            [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => UnavailableLocation::class,
                'method' => 'serializeUnavailableLocation',
            ],
            [
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'type' => UnavailableLocation::class,
                'method' => 'deserializeLocation',
            ],
        ];
    }

    /**
     * @param \JMS\Serializer\JsonSerializationVisitor $visitor
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     *
     * @return array
     */
    public function serializeLocation(
        JsonSerializationVisitor $visitor,
        Location $location
    ): array {
        return [
            'id' => $location->id,
            'contentInfo' => [
                'id' => $location->getContentInfo()->id,
                'name' => $location->getContentInfo()->name,
            ],
            'unavailable' => false,
        ];
    }

    /**
     * @param \JMS\Serializer\JsonDeserializationVisitor $visitor
     * @param array $data
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location|null
     */
    public function deserializeLocation(
        JsonDeserializationVisitor $visitor,
        array $data
    ): ?Location {
        try {
            return $this->locationService->loadLocation((int)$data['id']);
        } catch (Exception $e) {
            return new UnavailableLocation([
                'id' => $data['id'],
                'contentInfo' => new UnavailableContentInfo([
                    'id' => $data['contentInfo']['id'],
                    'name' => $data['contentInfo']['name'],
                ]),
            ]);
        }
    }

    /**
     * @param \JMS\Serializer\JsonSerializationVisitor $visitor
     * @param \Ibexa\FieldTypePage\ScheduleBlock\Item\UnavailableLocation $location
     *
     * @return array
     */
    public function serializeUnavailableLocation(
        JsonSerializationVisitor $visitor,
        UnavailableLocation $location
    ): array {
        return array_replace($this->serializeLocation($visitor, $location), ['unavailable' => true]);
    }
}

class_alias(LocationHandler::class, 'EzSystems\EzPlatformPageFieldType\Serializer\Handler\LocationHandler');
