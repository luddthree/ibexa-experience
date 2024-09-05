<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\PageBuilder\Timeline\Serializer\Driver;

use Ibexa\Contracts\PageBuilder\PageBuilder\Timeline\BaseEvent;
use JMS\Serializer\Metadata\Driver\YamlDriver;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use Metadata\ClassMetadata as BaseClassMetadata;
use Metadata\Driver\FileLocator;

/**
 * @internal
 */
final class ConfigurationSpoofingYamlDriver extends YamlDriver
{
    /** @var string[] */
    private $eventTypeMap;

    /**
     * @param \Metadata\Driver\FileLocator $fileLocator
     * @param \JMS\Serializer\Naming\PropertyNamingStrategyInterface $namingStrategy
     * @param string[] $eventTypeMap
     */
    public function __construct(FileLocator $fileLocator, PropertyNamingStrategyInterface $namingStrategy, array $eventTypeMap)
    {
        parent::__construct($fileLocator, $namingStrategy);

        $this->eventTypeMap = $eventTypeMap;
    }

    /**
     * {@inheritdoc}
     */
    protected function loadMetadataFromFile(\ReflectionClass $class, string $file): ?BaseClassMetadata
    {
        /** @var \JMS\Serializer\Metadata\ClassMetadata $metadata */
        $metadata = parent::loadMetadataFromFile($class, $file);

        if ($metadata->name === BaseEvent::class) {
            $metadata->setDiscriminator('type', array_merge($metadata->discriminatorMap, $this->eventTypeMap));
        }

        return $metadata;
    }
}

class_alias(ConfigurationSpoofingYamlDriver::class, 'EzSystems\EzPlatformPageBuilder\PageBuilder\Timeline\Serializer\Driver\ConfigurationSpoofingYamlDriver');
