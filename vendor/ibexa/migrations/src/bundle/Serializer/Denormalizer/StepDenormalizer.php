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
use function sprintf;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Webmozart\Assert\Assert;

final class StepDenormalizer implements LoggerAwareInterface, DenormalizerInterface, DenormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use DenormalizerAwareTrait;
    use LoggerAwareTrait;

    public const STEP_NUMBER_CONTEXT_KEY = 'step_number';

    /** @var array<string, array<string, class-string>> */
    private $typeMap = [];

    /**
     * @param iterable<\Ibexa\Bundle\Migration\Serializer\Denormalizer\StepNormalizerInterface> $stepNormalizers
     */
    public function __construct(iterable $stepNormalizers)
    {
        foreach ($stepNormalizers as $stepNormalizer) {
            $type = $stepNormalizer->getType();
            $mode = $stepNormalizer->getMode();

            $this->typeMap[$type][$mode] = $stepNormalizer->getHandledClassType();
        }
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === StepInterface::class;
    }

    /**
     * @param array<mixed> $data
     * @param array<string, mixed> $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\StepInterface
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        Assert::isArray($data);
        Assert::keyExists($data, 'type', 'Step `type` key is missing.');
        Assert::keyExists($data, 'mode', 'Step `mode` key is missing.');

        $stepType = $this->mapTypeModeToClass($data['type'], $data['mode']);

        $this->getLogger()->info(implode(' | ', [
            sprintf('Type "%s"', $data['type']),
            sprintf('Mode: "%s"', $data['mode']),
            sprintf('Matching step: "%s"', $stepType),
        ]));

        $step = $this->denormalizer->denormalize($data, $stepType, $format, $context);
        Assert::isInstanceOf($step, StepInterface::class);

        return $step;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    private function mapTypeModeToClass(string $type, string $mode): string
    {
        if (isset($this->typeMap[$type][$mode])) {
            return $this->typeMap[$type][$mode];
        }

        throw new InvalidArgumentException(sprintf(
            'Unknown step. Unable to match Step denormalizer to type: %s, mode: %s.',
            $type,
            $mode
        ));
    }
}

class_alias(StepDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Denormalizer\StepDenormalizer');
