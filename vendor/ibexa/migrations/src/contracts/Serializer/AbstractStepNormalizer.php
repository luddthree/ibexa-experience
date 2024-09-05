<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Migration\Serializer;

use Ibexa\Bundle\Migration\Serializer\Denormalizer\StepNormalizerInterface;
use Ibexa\Migration\ValueObject;
use Ibexa\Migration\ValueObject\Step\Action;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

/**
 * @template T of \Ibexa\Migration\ValueObject\Step\StepInterface
 */
abstract class AbstractStepNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface, StepNormalizerInterface, CacheableSupportsMethodInterface
{
    public const CONTEXT_STEP_CLASS_KEY = 'step_class';

    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;

    /** @var string */
    private $forbiddenNormalizeStepKeyMessage;

    /**
     * @param T $object
     * @param string|null $format
     * @param array<mixed> $context
     *
     * @return array<mixed>
     */
    final public function normalize($object, string $format = null, array $context = [])
    {
        $data = $this->normalizeStep(
            $object,
            $format,
            [self::CONTEXT_STEP_CLASS_KEY => $this->getHandledClassType()] + $context,
        );

        Assert::keyNotExists($data, 'type', $this->getForbiddenNormalizeStepKeyMessage());
        Assert::keyNotExists($data, 'mode', $this->getForbiddenNormalizeStepKeyMessage());

        $data = array_merge([
            'type' => $this->getType(),
            'mode' => $this->getMode(),
        ], $data);

        if ($object instanceof ValueObject\Step\ActionsAwareStepInterface) {
            $actions = $this->normalizer->normalize($object->getActions(), $format, $context);

            if (!empty($actions)) {
                $data['actions'] = $actions;
            }
        }

        return $data;
    }

    /**
     * @phpstan-param T $object
     *
     * @param array<mixed> $context
     *
     * @return array<string, mixed>
     */
    abstract protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array;

    /**
     * @param array<mixed> $data
     * @param array<mixed> $context
     *
     * @phpstan-return T
     */
    final public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $step = $this->denormalizeStep(
            $data,
            $type,
            $format ?? '',
            [self::CONTEXT_STEP_CLASS_KEY => $this->getHandledClassType()] + $context,
        );
        if ($step instanceof ValueObject\Step\ActionsAwareStepInterface) {
            $actions = $this->denormalizeActions($data, $format, $context);
            foreach ($actions as $action) {
                $step->addAction($action);
            }
        }

        return $step;
    }

    /**
     * @param array<mixed> $data
     * @param string|null $format
     * @param array<mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\Action[]
     */
    private function denormalizeActions(array $data, ?string $format, array $context): array
    {
        $actions = [];
        // BC for actions defined as an empty object
        if (!empty($data['actions'])) {
            Assert::isArray($data['actions'], '"actions" need to be an array');

            /** @var \Ibexa\Migration\ValueObject\Step\Action[] $actions */
            $actions = $this->denormalizer->denormalize(
                $data['actions'],
                Action::class . '[]',
                $format,
                [self::CONTEXT_STEP_CLASS_KEY => $this->getHandledClassType()] + $context,
            );
        }

        return $actions;
    }

    /**
     * @param array<mixed> $data
     * @param array<mixed> $context
     *
     * @phpstan-return T
     */
    abstract protected function denormalizeStep($data, string $type, string $format, array $context = []): StepInterface;

    /** @phpstan-return class-string<T> */
    abstract public function getHandledClassType(): string;

    final public function supportsNormalization($data, string $format = null)
    {
        return is_a($data, $this->getHandledClassType());
    }

    final public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === $this->getHandledClassType();
    }

    final public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    private function getForbiddenNormalizeStepKeyMessage(): string
    {
        if (!isset($this->forbiddenNormalizeStepKeyMessage)) {
            $this->forbiddenNormalizeStepKeyMessage = sprintf(
                'Expected %%s to not be set by %s::%s method.',
                static::class,
                'normalizeStep',
            );
        }

        return $this->forbiddenNormalizeStepKeyMessage;
    }
}

class_alias(AbstractStepNormalizer::class, 'Ibexa\Platform\Contracts\Migration\Serializer\AbstractStepNormalizer');
