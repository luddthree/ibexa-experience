<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface;
use LogicException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

/**
 * @template T of \Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface
 */
abstract class AbstractCriterionNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @return non-empty-string
     */
    abstract protected function getHandledType(): string;

    /**
     * @return class-string<T>
     */
    abstract protected function getHandledClass(): string;

    /**
     * @phpstan-param array<string, mixed>|array{type: string} $data
     *
     * @param array<string, mixed> $context
     */
    abstract protected function doDenormalize(array $data, string $type, ?string $format, array $context): CriterionInterface;

    /**
     * @param T $object
     * @param array<string, mixed> $context
     *
     * @phpstan-return array<string, mixed>|array{type: string}
     */
    abstract protected function doNormalize(CriterionInterface $object, string $format = null, array $context = []): array;

    final public function denormalize($data, string $type, string $format = null, array $context = []): CriterionInterface
    {
        Assert::isArray($data);
        Assert::keyExists($data, 'type');

        return $this->doDenormalize($data, $type, $format, $context);
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return $type === CriterionInterface::class && isset($data['type']) && $data['type'] === $this->getHandledType();
    }

    /**
     * @param T $object
     *
     * @return array<string, mixed>
     */
    final public function normalize($object, string $format = null, array $context = []): array
    {
        $data = $this->doNormalize($object, $format, $context);

        if (isset($data['type'])) {
            throw new LogicException(sprintf(
                'Setting "type" property is in "%s::doNormalize" forbidden. It is reserved for Criterion type detection.',
                static::class,
            ));
        }

        return [
            'type' => $this->getHandledType(),
        ] + $data;
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        $handledClass = $this->getHandledClass();

        return $data instanceof $handledClass;
    }
}
