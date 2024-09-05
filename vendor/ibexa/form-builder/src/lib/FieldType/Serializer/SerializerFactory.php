<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FieldType\Serializer;

use JMS\Serializer\ContextFactory\DeserializationContextFactoryInterface;
use JMS\Serializer\ContextFactory\SerializationContextFactoryInterface;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\Visitor\Factory\JsonDeserializationVisitorFactory;
use JMS\Serializer\Visitor\Factory\JsonSerializationVisitorFactory;
use Symfony\Component\HttpKernel\KernelInterface;

class SerializerFactory
{
    /** @var \Symfony\Component\HttpKernel\KernelInterface */
    protected $kernel;

    /** @var \JMS\Serializer\ContextFactory\SerializationContextFactoryInterface */
    protected $serializationContextFactory;

    /** @var \JMS\Serializer\ContextFactory\DeserializationContextFactoryInterface */
    protected $deserializationContextFactory;

    /**
     * @param \Symfony\Component\HttpKernel\KernelInterface $kernel
     * @param \JMS\Serializer\ContextFactory\SerializationContextFactoryInterface $serializationContextFactory
     * @param \JMS\Serializer\ContextFactory\DeserializationContextFactoryInterface $deserializationContextFactory
     */
    public function __construct(
        KernelInterface $kernel,
        SerializationContextFactoryInterface $serializationContextFactory,
        DeserializationContextFactoryInterface $deserializationContextFactory
    ) {
        $this->kernel = $kernel;
        $this->serializationContextFactory = $serializationContextFactory;
        $this->deserializationContextFactory = $deserializationContextFactory;
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
        $bundleDir = $this->kernel->locateResource('@IbexaFormBuilderBundle');

        $serializerBuilder = SerializerBuilder::create();
        $serializerBuilder
            ->setSerializationContextFactory($this->serializationContextFactory)
            ->setDeserializationContextFactory($this->deserializationContextFactory)
            ->addDefaultDeserializationVisitors()
            ->addDefaultSerializationVisitors()
            ->setPropertyNamingStrategy(new CamelCaseNamingStrategy())
            ->setSerializationVisitor('json', new JsonSerializationVisitorFactory())
            ->setDeserializationVisitor('json', new JsonDeserializationVisitorFactory())
            ->setMetadataDirs(
                [
                    'Ibexa\\Contracts\\FormBuilder' => $bundleDir . 'Resources/config/serializer',
                ]
            );

        return $serializerBuilder->build();
    }
}

class_alias(SerializerFactory::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Serializer\SerializerFactory');
