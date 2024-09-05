<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer;

use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Throwable;
use Webmozart\Assert\Assert;

final class StepsDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface, CacheableSupportsMethodInterface, LoggerAwareInterface
{
    use DenormalizerAwareTrait;
    use LoggerAwareTrait;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === (StepInterface::class . '[]');
    }

    /**
     * @param array<mixed> $data
     * @param array<string, mixed> $context
     *
     * @return \Traversable<\Ibexa\Migration\ValueObject\Step\StepInterface>
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        Assert::notNull($data, 'Invalid migration data.');
        Assert::isIterable($data, 'Invalid migration data.');

        foreach ($data as $stepNum => $step) {
            $this->getLogger()->info(sprintf('Processing step: %s', $stepNum));

            try {
                $step = $this->denormalizer->denormalize($step, StepInterface::class, $format, $context);

                yield $step;
            } catch (Throwable $e) {
                if ($e instanceof NotNormalizableValueException && isset($step['type'], $step['mode'])) {
                    $e = new InvalidArgumentException(
                        sprintf(
                            'Unknown step. Unable to match Step denormalizer to type: %s, mode: %s.',
                            $step['type'],
                            $step['mode'],
                        ),
                        $e->getCode(),
                        $e,
                    );
                }

                $exception = new StepDenormalizationException($stepNum, $e);

                if ($context['discard_invalid_steps'] ?? false) {
                    if (isset($context['errors_collection'])) {
                        $context['errors_collection'][] = $exception;
                    }

                    continue;
                }

                throw $exception;
            }
        }
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}

class_alias(StepsDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Denormalizer\StepsDenormalizer');
