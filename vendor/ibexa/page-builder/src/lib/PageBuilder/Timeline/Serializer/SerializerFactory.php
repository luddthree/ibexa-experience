<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\PageBuilder\Timeline\Serializer;

use JMS\Serializer\Builder\DriverFactoryInterface;
use JMS\Serializer\ContextFactory\DefaultDeserializationContextFactory;
use JMS\Serializer\ContextFactory\DefaultSerializationContextFactory;
use JMS\Serializer\ContextFactory\DeserializationContextFactoryInterface;
use JMS\Serializer\ContextFactory\SerializationContextFactoryInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\Visitor\Factory\JsonDeserializationVisitorFactory;
use JMS\Serializer\Visitor\Factory\JsonSerializationVisitorFactory;
use Symfony\Component\Config\FileLocatorInterface;

class SerializerFactory
{
    /** @var \JMS\Serializer\Builder\DriverFactoryInterface */
    private $driverFactory;

    /** @var \Symfony\Component\Config\FileLocatorInterface */
    private $fileLocator;

    /** @var \JMS\Serializer\ContextFactory\SerializationContextFactoryInterface|null */
    private $serializationContextFactory;

    /** @var \JMS\Serializer\ContextFactory\DeserializationContextFactoryInterface|null */
    private $deserializationContextFactory;

    /** @var string[] */
    private $metadataDirs;

    /**
     * @param \JMS\Serializer\Builder\DriverFactoryInterface $driverFactory
     * @param \Symfony\Component\Config\FileLocatorInterface $fileLocator
     * @param \JMS\Serializer\ContextFactory\SerializationContextFactoryInterface|null $serializationContextFactory
     * @param \JMS\Serializer\ContextFactory\DeserializationContextFactoryInterface|null $deserializationContextFactory
     * @param string[] $metadataDirs
     */
    public function __construct(
        DriverFactoryInterface $driverFactory,
        FileLocatorInterface $fileLocator,
        ?SerializationContextFactoryInterface $serializationContextFactory = null,
        ?DeserializationContextFactoryInterface $deserializationContextFactory = null,
        array $metadataDirs = []
    ) {
        $this->driverFactory = $driverFactory;
        $this->fileLocator = $fileLocator;
        $this->serializationContextFactory = $serializationContextFactory ?: new DefaultSerializationContextFactory();
        $this->deserializationContextFactory = $deserializationContextFactory ?: new DefaultDeserializationContextFactory();
        $this->metadataDirs = $metadataDirs;
    }

    /**
     * @return \JMS\Serializer\SerializerInterface
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \JMS\Serializer\Exception\InvalidArgumentException
     */
    public function create(): SerializerInterface
    {
        $serializerBuilder = SerializerBuilder::create();

        $serializerBuilder
            ->setMetadataDriverFactory($this->driverFactory)
            ->setSerializationContextFactory([$this, 'getSerializationContext'])
            ->setDeserializationContextFactory([$this, 'getDeserializationContext'])
            ->addDefaultDeserializationVisitors()
            ->addDefaultSerializationVisitors()
            ->setSerializationVisitor('json', new JsonSerializationVisitorFactory())
            ->setDeserializationVisitor('json', new JsonDeserializationVisitorFactory())
            ->addMetadataDirs($this->resolveMetadataDirs($this->metadataDirs))
        ;

        return $serializerBuilder->build();
    }

    /**
     * @return string[]
     */
    public function getMetadataDirs(): array
    {
        return $this->metadataDirs;
    }

    /**
     * @param string[] $metadataDirs
     */
    public function setMetadataDirs(array $metadataDirs): void
    {
        $this->metadataDirs = $metadataDirs;
    }

    /**
     * @return \JMS\Serializer\SerializationContext
     */
    public function getSerializationContext(): SerializationContext
    {
        $serializationContext = $this->serializationContextFactory->createSerializationContext();

        return $serializationContext;
    }

    /**
     * @return \JMS\Serializer\DeserializationContext
     */
    public function getDeserializationContext(): DeserializationContext
    {
        $deserializationContext = $this->deserializationContextFactory->createDeserializationContext();

        return $deserializationContext;
    }

    /**
     * Resolves resource names in metadata directories.
     *
     * @param string[] $metadataDirs
     *
     * @return string[]
     */
    private function resolveMetadataDirs(array $metadataDirs): array
    {
        return array_combine(
            array_keys($metadataDirs),
            array_map([$this->fileLocator, 'locate'], $metadataDirs)
        );
    }
}

class_alias(SerializerFactory::class, 'EzSystems\EzPlatformPageBuilder\PageBuilder\Timeline\Serializer\SerializerFactory');
