<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Bundle\Migration\Serializer\Denormalizer\StepNormalizerInterface;
use Ibexa\Migration\ValueObject;
use Psr\Container\ContainerInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class ServiceCallStepNormalizer implements StepNormalizerInterface, DenormalizerInterface, NormalizerInterface, CacheableSupportsMethodInterface
{
    /** @var \Psr\Container\ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getType(): string
    {
        return 'service_call';
    }

    public function getMode(): string
    {
        return 'execute';
    }

    public function getHandledClassType(): string
    {
        return ValueObject\Step\ServiceCallExecuteStep::class;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\ServiceCallExecuteStep $object
     * @param array<string, mixed> $context
     *
     * @return array{
     *     type: string,
     *     mode: string,
     *     service: string,
     *     method: ?string,
     *     arguments?: array<mixed>,
     * }
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        Assert::isInstanceOf($object, ValueObject\Step\ServiceCallExecuteStep::class);

        $data = [
            'type' => $this->getType(),
            'mode' => $this->getMode(),
            'service' => $object->service,
            'method' => $object->method,
        ];

        if ($object->arguments) {
            $data['arguments'] = $object->arguments;
        }

        return $data;
    }

    /**
     * @param array{
     *     type: string,
     *     mode: string,
     *     service: mixed,
     *     method: ?string,
     *     arguments?: array<mixed>,
     * } $data
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\ServiceCallExecuteStep
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        Assert::stringNotEmpty($data['service']);
        $service = $data['service'];

        Assert::true(
            $this->container->has($service),
            sprintf('Unable to call service "%s". Make sure it is defined in migration configuration.', $service)
        );

        return new ValueObject\Step\ServiceCallExecuteStep(
            $service,
            $data['arguments'] ?? [],
            $data['method'] ?? null,
        );
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof ValueObject\Step\ServiceCallExecuteStep;
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === ValueObject\Step\ServiceCallExecuteStep::class;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(ServiceCallStepNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Step\ServiceCallStepNormalizer');
