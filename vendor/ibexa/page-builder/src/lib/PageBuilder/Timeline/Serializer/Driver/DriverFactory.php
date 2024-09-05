<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\PageBuilder\Timeline\Serializer\Driver;

use Doctrine\Common\Annotations\Reader;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use JMS\Serializer\Builder\DriverFactoryInterface;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use Metadata\Driver\DriverInterface;
use Metadata\Driver\FileLocator;

/**
 * @internal
 */
final class DriverFactory implements DriverFactoryInterface
{
    /** @var string[] */
    private $eventTypeMap;

    /**
     * @param string[] $eventTypeMap
     */
    public function __construct(array $eventTypeMap)
    {
        $this->eventTypeMap = $eventTypeMap;
    }

    public function createDriver(array $metadataDirs, ?Reader $annotationReader = null): DriverInterface
    {
        if (!empty($metadataDirs)) {
            $fileLocator = new FileLocator($metadataDirs);

            return new ConfigurationSpoofingYamlDriver(
                $fileLocator,
                new IdenticalPropertyNamingStrategy(),
                $this->eventTypeMap
            );
        }

        if (null === $annotationReader) {
            throw new InvalidArgumentException(
                '$annotationReader',
                'Failed to create Driver. AnnotationReader is required.'
            );
        }

        return new AnnotationDriver($annotationReader, new IdenticalPropertyNamingStrategy());
    }
}

class_alias(DriverFactory::class, 'EzSystems\EzPlatformPageBuilder\PageBuilder\Timeline\Serializer\Driver\DriverFactory');
