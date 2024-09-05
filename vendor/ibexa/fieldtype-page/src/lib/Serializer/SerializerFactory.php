<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Serializer;

use JMS\Serializer\ContextFactory\DeserializationContextFactoryInterface;
use JMS\Serializer\ContextFactory\SerializationContextFactoryInterface;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class SerializerFactory
{
    /** @var \Symfony\Component\HttpKernel\KernelInterface */
    protected $kernel;

    /** @var \JMS\Serializer\ContextFactory\SerializationContextFactoryInterface */
    protected $serializationContextFactory;

    /** @var \JMS\Serializer\ContextFactory\DeserializationContextFactoryInterface */
    protected $deserializationContextFactory;

    /** @var \JMS\Serializer\Handler\SubscribingHandlerInterface[] */
    protected $handlers;

    /** @var string */
    private $cacheDir;

    public function __construct(
        KernelInterface $kernel,
        SerializationContextFactoryInterface $serializationContextFactory,
        DeserializationContextFactoryInterface $deserializationContextFactory,
        iterable $handlers = [],
        string $cacheDir = ''
    ) {
        $this->kernel = $kernel;
        $this->serializationContextFactory = $serializationContextFactory;
        $this->deserializationContextFactory = $deserializationContextFactory;
        $this->handlers = $handlers;
        $this->cacheDir = $cacheDir;
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
        $bundleDir = $this->kernel->locateResource('@IbexaFieldTypePageBundle');

        $serializerBuilder = SerializerBuilder::create();
        $serializerBuilder
            ->setSerializationContextFactory($this->serializationContextFactory)
            ->setDeserializationContextFactory($this->deserializationContextFactory)
            ->addDefaultDeserializationVisitors()
            ->addDefaultSerializationVisitors()
            ->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy())
            ->configureHandlers(\Closure::fromCallable([$this, 'configureHandlers']))
            ->addDefaultHandlers()
            ->setMetadataDirs(
                [
                    'Ibexa\\Contracts\\FieldTypePage' => $bundleDir . 'Resources/config/serializer',
                    'Ibexa\\FieldTypePage' => $bundleDir . 'Resources/config/serializer',
                ]
            );

        if (!empty($this->cacheDir)) {
            $serializerBuilder->setCacheDir($this->cacheDir);
        }

        return $serializerBuilder->build();
    }

    public function configureHandlers(HandlerRegistry $handlerRegistry): void
    {
        foreach ($this->handlers as $handler) {
            $handlerRegistry->registerSubscribingHandler($handler);
        }
    }
}

class_alias(SerializerFactory::class, 'EzSystems\EzPlatformPageFieldType\Serializer\SerializerFactory');
